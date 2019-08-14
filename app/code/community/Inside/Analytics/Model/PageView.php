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
     * Is Amasty Improved Navigation extension active?
     * 
     * @var boolean 
     */
    protected $_isAmasty = false;
    
    
    public function _construct()
    {
	parent::_construct();
	$this->_pageViewType = Mage::getModel('inside/pageView_type');
	$this->_loadProduct()->_loadCategory();
    }
    
    /**
     * Gets array of pageTrack required data
     * 
     * @param string $fullActionName
     * @return array
     */
    public function getPageTrackCodeData($requestArray)
    {
	$this->_setAmasty($requestArray);
	$type = $this->_pageViewType->getPageType($requestArray);
	$common = array(
	    'action' => 'trackView',
	    'type' => $type,
	    'name' => $this->_pageViewType->getPageName($type),
	    'orderId' => $this->_getOrderId(),
	    'orderTotal' => $this->_getOrderTotal(),
	    'shippingTotal' => $this->_getShippingTotal()
	);
	$pageSpecificData = $this->_getPageSpecificByType($type);
	return array_merge($common, $pageSpecificData);
    }
    
    /**
     * Gets array of order track data
     * 
     * @param string $fullActionName
     * @return array
     */
    public function getOrderTrackCodeData($requestArray)
    {
	$items = array();
	$type = $this->_pageViewType->getPageType($requestArray);
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
	$extra = array();
	switch ($type) {
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::CATEGORY:
		if ($this->_isAmasty) {
		    $categoryArr = array_reverse(Mage::helper('inside')->getAmastyCategory());
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
		$extra['name'] = Mage::app()->getLayout()->getBlock('head')->getTitle();
		break;
		
	}
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
	return $id;
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
	if ($product && $product->getId()) {
	    $this->_product = $product;
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
	if ($category && $category->getId()) {
	    $this->_category = $category;
	}
	return $this;
    }
    
    /**
     * Checks for Amasty improved navigation extension
     * 
     * @param array $requestArr
     * @return \Inside_Analytics_Model_PageView
     */
    protected function _setAmasty($requestArr)
    {
	if ($requestArr[Inside_Analytics_Helper_Data::REQUEST_PART_MODULE] == 'amshopby') {
	    $this->_isAmasty = true;
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
	return Mage::getSingleton('core/session')->getInsideUpdateOnNext() === true;
    }
    
    /**
     * Resets Ajax call related session variables
     */
    protected function _resetSession()
    {
	Mage::getSingleton('core/session')->unsetData('inside_update_on_next');
    }
    
}

