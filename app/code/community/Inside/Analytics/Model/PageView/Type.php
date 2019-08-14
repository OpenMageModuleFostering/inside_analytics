<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Model_PageView_Type extends Mage_Core_Model_Abstract {

    /**
     * List of page view types and associated route names
     * @var array
     */
    protected $_typeActions = array();
    
    public function _construct()
    {
	$this->_loadTrackRoutes();
    }
    
    /**
     * Loads array of page types and configured routes
     * 
     * @return \Inside_Analytics_Model_PageView_Type
     */
    protected function _loadTrackRoutes()
    {
	$routes = Mage::getResourceModel('inside/route_collection')
		->addFieldToFilter('is_active', true);
	foreach($routes as $route) {
	    /* @var $route Inside_Analytics_Model_Route */
	    if (array_key_exists($route->getType(), $this->_typeActions)) {
		$this->_typeActions[$route->getType()][] = $route->getFullQualifier();
	    } else {
		$this->_typeActions[$route->getType()] = array($route->getFullQualifier());
	    }
	}
	return $this;
    }
    
    /**
     * Get list of allowed page view types
     * 
     * @return array
     */
    public function getAllowedPageViewTypes()
    {
	return array_keys($this->_typeActions);
    }
    
    /**
     * Get associative array of all allowed page view types
     * and their action names
     * 
     * @return array
     */
    public function getTypeActions()
    {
	return $this->_typeActions;
    }
    
    /**
     * Get page type based on the full action name
     * 
     * @param string $fullActionName
     * @return string
     */
    public function getPageType($requestArray)
    {
	$fullActionName = implode('_', array_values($requestArray));
	$match = $this->_getPageTypeExact($fullActionName);
	if (!$match) {
	    $found = false;
	    foreach ($this->getTypeActions() as $type => $actions)
	    {
		foreach ($actions as $action) {
		    $_t = explode('_', $action);
		    switch (count($_t)) 
		    {
			case 1: //match only module name
			    if ($_t[0] === $requestArray[Inside_Analytics_Helper_Data::REQUEST_PART_MODULE]) {
				$match = $type; $found = true;
			    }
			    break;
			case 2: //match module and controller name
			    if ($_t[0] === $requestArray[Inside_Analytics_Helper_Data::REQUEST_PART_MODULE] &&
				$_t[1] === $requestArray[Inside_Analytics_Helper_Data::REQUEST_PART_CONTROLLER]) 
			    {
				$match = $type; $found = true;
			    }
			    break;
		    }
		    if ($found) break;
		}
		if ($found) break;
	    }
	}
	return $match ? $match : Inside_Analytics_Model_System_Config_Source_Page_Type::OTHER;
    }
    
    /**
     * Search for exact full action name match in page type actions
     * 
     * @param string $key
     * @return string Page type or false if not found
     */
    protected function _getPageTypeExact($key) 
    {
	foreach ($this->getTypeActions() as $type => $actions)
	{
	    if (in_array($key, $actions)) {
		return $type;
	    }
	}
	return false;
    }
    
    /**
     * Get page name based on it's type
     * 
     * @param string $type
     * @return string
     */
    public function getPageName($type) 
    {
	$name = 'Unknown/Untracked Page Type';
	switch ($type) {
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::HOMEPAGE: 
		$name = 'Home Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::ARTICLE:  
		$name = 'Information Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::SEARCH:	 
		$name = 'Search Result Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::CATEGORY: 
		$name = 'Product Category Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::PRODUCT:  
		$name = 'Product Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::LOGIN:    
		$name = 'Login Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::CHECKOUT: 
		$name = 'Checkout/Cart Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::ORDERCONFIRMED: 
		$name = 'Order Confirmation Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::LEAD:     
		$name = 'Lead Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::LEADCONFIRMED:  
		$name = 'Lead Confirmation Page'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::NOTFOUND: 
		$name = 'Page Not Found (404)'; break;
	    case Inside_Analytics_Model_System_Config_Source_Page_Type::OTHER:
	    default:
		$name = 'Page Type Not Available'; break;
	}
	
	return $name;
    }
}