<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE  = 'inside/analytics/active';
    const XML_PATH_ACCOUNT = 'inside/analytics/account';
    const XML_LOG_ACTIVE   = 'inside/debug/log';
    const XML_SHOW_REQUEST = 'inside/debug/show';
    
    const REQUEST_PART_MODULE	  = 'module';
    const REQUEST_PART_CONTROLLER = 'controller';
    const REQUEST_PART_ACTION	  = 'action';
    
    /**
     * Whether IA is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isInsideAnalyticsAvailable($store = null)
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
        return $accountId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }
    
    /**
     * Get customerId string for the main getTracker script
     * customer email is a md5 string (logged in customers only)
     */
    public function getVisitorId()
    {
	if (Mage::helper('customer')->isLoggedIn()) {
	    $customer = Mage::helper('customer')->getCustomer();
	    /* @var $customer Mage_Customer_Model_Customer */
	    $emailAddress = $customer->getEmail();
	    if (strlen($emailAddress)) {
		return ', \'visitorId\': \''.md5($emailAddress).'\'';
	    }
	}
	return '';
    }
    
    /**
     * Get customer full name for the main getTracker script
     * @return string
     */
    public function getVisitorName()
    {
	if (Mage::helper('customer')->isLoggedIn()) {
	    $customer = Mage::helper('customer')->getCustomer();
	    /* @var $customer Mage_Customer_Model_Customer */
	    $fullName = $customer->getName();
	    if (strlen($fullName)) {
		return ', \'visitorName\': \''.$fullName.'\'';
	    }
	}
	return '';
    }
    
    /**
     * Gets product image Url (thumbnail)
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return string|null
     */
    public function getProductImageUrl(Mage_Catalog_Model_Product $product)
    {
	return sprintf('%s', Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(256));
    }
    
    /**
     * Gets combined parent category names this product/category belongs to.
     * 
     * @param Varien_Object $object
     * @return string
     */
    public function getFullCategoryName(Varien_Object $object)
    {
	$categoryName = array();
	if ($object instanceof Mage_Catalog_Model_Product) {
	    $category = $object->getCategory();
	} else {
	    $category = $object;
	}
	if ($category instanceof Mage_Catalog_Model_Category) {
	    $categoryName[] = $category->getName();
	    $parentCategory = $category->getParentCategory();
	    while ($parentCategory instanceof Mage_Catalog_Model_Category 
		    && $parentCategory->getParentId() > 1) 
	    {
		$categoryName[] = $parentCategory->getName();
		$parentCategory = $parentCategory->getParentCategory();
	    }
	}
	return array_reverse($categoryName);
    }
    
    /**
     * Gets category array from page title (Amasty improved navigation)
     * 
     * @return array
     */
    public function getAmastyCategory()
    {
	$title = explode(' - ', Mage::app()->getLayout()->getBlock('head')->getTitle());
	return $title;
    }
    
    /**
     * Is extension logging enabled?
     * 
     * @param int $store
     * @return boolean
     */
    public function isLoggingEnabled($store = null)
    {
	return Mage::getStoreConfigFlag(self::XML_LOG_ACTIVE, $store);
    }
    
    /**
     * Can request array be shown?
     * 
     * @param int $store
     * @return boolean
     */
    public function canShowRequest($store = null)
    {
	return Mage::getStoreConfigFlag(self::XML_SHOW_REQUEST, $store);
    }
    
    /**
     * Returns all search parameter names available
     * 
     * @return array
     */
    public function getAllSearchParams()
    {
	$searchParams = array();
	$collection = Mage::getResourceModel('inside/route_collection')
		->addFieldToFilter('search_param', array('notnull' => true));
	foreach ($collection as $route) {
	    $searchParams[] = $route->getSearchParam();
	}
	return $searchParams;
    }
}