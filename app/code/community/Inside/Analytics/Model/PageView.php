<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Model_PageView extends Mage_Core_Model_Abstract {
    
    /**
     * @var Inside_Analytics_Model_PageView_Type
     */
    protected $_pageViewType = null;
    
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * @var Mage_Catalog_Model_Category 
     */
    protected $_category = null;
    
    /**
     * @var boolean
     */
    protected $_outOfStock = false;
       
    
    /**
     * Build category name from page title
     * (applies to Amasty Improved Navigation and similar modules)
     * 
     * @var boolean 
     */
    protected $_categoryFromTitle = false;
    
    
    public function _construct()
    {
	parent::_construct();
	$this->_pageViewType = Mage::getModel('inside/pageView_type');
	$this->_categoryFromTitle = Mage::helper('inside')->buildCategoryFromTitle();
	$this->_loadProduct()->_loadCategory();
    }
    
    /**
     * Gets array of pageTrack required data
     * 
     * @param string $requestArray
     * @return array
     */
    public function getPageTrackCodeData($requestArray)
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$type = $this->_pageViewType->getPageType($requestArray);
	$common = array(
	    'action' => 'trackView',
	    'type' => $type,
	    'name' => $this->_pageViewType->getPageName($type),
	    'orderId' => $this->_getOrderId(),
	    'orderTotal' => $this->_getOrderTotal(),
	    'shippingTotal' => $this->_getShippingTotal(),
	    'tags' => $this->_getTags($type),
	);
	$pageSpecificData = $this->_getPageSpecificByType($type);
	
	Mage::helper('inside')->log('$pageSpecificData: '.print_r($pageSpecificData, true), true);
	Mage::helper('inside')->log('$common: '.print_r($common, true), true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	
	return array_merge($common, $pageSpecificData);
    }
    
    /**
     * Gets array of order track data
     * 
     * @param string $requestArray
     * @return array
     */
    public function getOrderTrackCodeData($requestArray)
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$items = array();
	$type = $this->_pageViewType->getPageType($requestArray);
	
	Mage::helper('inside')->log('$type: '.$type, true);
	Mage::helper('inside')->log('$this->_getQuote()->getItemsCount(): '.$this->_getQuote()->getItemsCount(), true);
	
	if (($type == Inside_Analytics_Model_System_Config_Source_Page_Type::CHECKOUT || $this->_isForcedOrderUpdate()) 
		&& $this->_getQuote()->getItemsCount() > 0)
	{
	    foreach ($this->_getQuote()->getAllVisibleItems() as $item)
	    {
		/* @var $item Mage_Sales_Model_Quote_Item */
		$items[] = array(
		    'action'	=> 'addItem',
		    'orderId'	=> $this->_getOrderId(),
		    'sku'	=> $item->getSku(),
		    'name'	=> $item->getName(),
		    'img'	=> Mage::helper('inside')->getProductImageUrl($item->getProduct()),
		    'price'	=> $item->getPriceInclTax() ? $item->getPriceInclTax() : $item->getPrice(),
		    'qty'	=> $item->getQty()
		);
	    }
	    //Add track order array
	    $items[] = array(
		'action'    => 'trackOrder',
		'orderId'   => $this->_getOrderId(),
		'orderTotal'=> $this->_getOrderTotal(),
		'shippingTotal' => $this->_getShippingTotal(),
		'update'    => 'false'
	    );
	}
	if ($this->_isForcedOrderUpdate()) {
	    $this->_resetSession();
	}
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	return $items;
    }
    
    /**
     * Gets specific data based on current page type
     * (ie product image on product pages)
     * 
     * @param string $type
     * @return string
     */
    protected function _getPageSpecificByType($type)
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	Mage::helper('inside')->log('$type: '.$type, true);
	
	$extra = array();
	switch ($type) {
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::CATEGORY:
		if ($this->_categoryFromTitle) {
		    $categoryArr = array_reverse(Mage::helper('inside')->getCategoryFromTitle());
		    $extra['name'] = array_pop($categoryArr);
		    $extra['category'] = implode(' / ', $categoryArr);
		    if (empty($extra['name']) && $this->_category instanceof Mage_Catalog_Model_Category) {
			//defaults to real category name
			$this->_category->getName();
		    }
		} else {
		    //standard Magento
		    if ($this->_category instanceof Mage_Catalog_Model_Category) {
			$catArr = Mage::helper('inside')->getFullCategoryName($this->_category);
			$extra['name'] = array_pop($catArr);
			$extra['category'] = implode(' / ', $catArr);
		    }
		}
		break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::PRODUCT:
		if ($this->_product  instanceof Mage_Catalog_Model_Product) {
		    if($categoryName = Mage::helper('inside')->getFullCategoryName($this->_product)) {
			$extra['category'] = implode(' / ', $categoryName);
		    }
		    if($imageUrl = Mage::helper('inside')->getProductImageUrl($this->_product)) {
			$extra['img'] = $imageUrl;
		    }
		    //rewrite page name to include product name
		    $extra['name'] = $this->_product->getName();
		}
		break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::SEARCH:
		$params  = Mage::helper('inside')->getAllSearchParams();
		foreach ($params as $param) {
		    if ($value = Mage::app()->getRequest()->getParam($param)) {
			$extra['name'] = $value;
			break;
		    }
		}
		break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::ARTICLE:
		$extra['name'] = Mage::helper('inside')->getPageTitle();
		break;
		
	}
	Mage::helper('inside')->log('$extra: '.print_r($extra, true), true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	return $extra;
    }
    
    /**
     * Get unique identifier of a page 
     * (used by Inside to distinguish different pages of the same type)
     * 
     * @deprecated Since v1.1.2 (Oct 7th 2013)
     * @param string $type
     * @return string
     */
    protected function _getPageUniqueId($type)
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	Mage::helper('inside')->log('$type: '.$type, true);
	
	$id = null;
	switch ($type) {
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::CATEGORY:
		if ($this->_category instanceof Mage_Catalog_Model_Category) {
		    $id = $this->_category->getId();
		}
		break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::PRODUCT:
		if ($this->_product  instanceof Mage_Catalog_Model_Product) {
		    $id = $this->_product->getId();
		}
		break;
	}
	Mage::helper('inside')->log('$id: '.$id, true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	
	return $id;
    }
    
    /**
     * Gets custom tags string
     * 
     * @param string $type The page type
     * @return string
     */
    protected function _getTags($type)
    {
	$return = sprintf('language:%s', Mage::app()->getStore()->getCode());
	if ($this->_outOfStock) {
	    $return.=', outofstock';
	}
	if ($type == Inside_Analytics_Model_System_Config_Source_Page_Type::SEARCH) {
	    if (Mage::registry('search_results_count') === 0) {
		$return.=', emptysearch';
	    }
	}
	return $return;
    }
    
    /**
     * Get checkout quote instance by current session
     * 
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
	return Mage::getSingleton('checkout/session')->getQuote();
    }
    
    /**
     * Return Quote ID (only if items in cart)
     * 
     * @return int|null
     */
    protected function _getOrderId()
    {
	if ($this->_getQuote()->getItemsCount() > 0) {
	    return $this->_getQuote()->getId();
	}
	return null;
    }
    
    /**
     * Return formatted grand total of the current quote if active
     * 
     * @return string
     */
    protected function _getOrderTotal()
    {
	if ($this->_getOrderId()) {
	    return sprintf('%.2f', $this->_getQuote()->getGrandTotal());
	}
	return null;
    }
    
    /**
     * Return formatted grand total of the current quote if active
     * 
     * @return string
     */
    protected function _getShippingTotal()
    {
	if ($this->_getOrderId()) {
	    return sprintf('%.2f', $this->_getQuote()->getShippingAddress()->getBaseShippingInclTax());
	}
	return null;
    }
    
    /**
     * Load current product from registry
     * @return \Inside_Analytics_Model_PageView
     */
    protected function _loadProduct()
    {
	$product = Mage::registry('current_product');
	if (!$product && Mage::getEdition() === Mage::EDITION_ENTERPRISE) {
	    //try to load from cache
	    $processor = Mage::getSingleton('enterprise_pagecache/processor');
	    $productId = $processor->getMetadata(Enterprise_PageCache_Model_Processor_Product::METADATA_PRODUCT_ID);
	    $product = Mage::getModel('catalog/product')->load($productId);
	}
	if ($product && $product->getId()) {
	    $this->_product = $product;
	    $this->_outOfStock = !$product->getData('is_in_stock');
	}
	return $this;
    }
    
    /**
     * Load current category from registry
     * @return \Inside_Analytics_Model_PageView
     */
    protected function _loadCategory()
    {
	$category = Mage::registry('current_category');
	if (!$category && Mage::getEdition() === Mage::EDITION_ENTERPRISE) {
	    //try to load from cache
	    $processor = Mage::getSingleton('enterprise_pagecache/processor');
	    $categoryId = $processor->getMetadata(Enterprise_PageCache_Model_Processor_Category::METADATA_CATEGORY_ID);
	    $category = Mage::getModel('catalog/category')->load($categoryId);
	}
	if ($category && $category->getId()) {
	    $this->_category = $category;
	}
	return $this;
    }
    
    /**
     * Check if we have outstanding Ajax add-to-cart call
     * 
     * @return boolean
     */
    protected function _isForcedOrderUpdate()
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	Mage::helper('inside')->log('session update on next: '.(string)Mage::getSingleton('core/session')->getInsideUpdateOnNext(), true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	return Mage::getSingleton('core/session')->getInsideUpdateOnNext() === true;
    }
    
    /**
     * Resets Ajax call related session variables
     */
    protected function _resetSession()
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	Mage::getSingleton('core/session')->unsetData('inside_update_on_next');
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
    }
    
}

