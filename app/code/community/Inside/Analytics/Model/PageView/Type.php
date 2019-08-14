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
     * List of page view types and associated routes
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
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$routes = Mage::getResourceModel('inside/route_collection')
		->addFieldToFilter('is_active', true);
	foreach($routes as $route) {
	    /* @var $route Inside_Analytics_Model_Route */
	    if (array_key_exists($route->getType(), $this->_typeActions)) {
		$this->_typeActions[$route->getType()][] = $route;
	    } else {
		$this->_typeActions[$route->getType()] = array($route);
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
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
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
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$fullActionName = implode('_', array_values($requestArray));
	$match = $this->_getPageTypeExact($fullActionName);
	if (!$match) {	    
	    foreach ($this->getTypeActions() as $type => $routes)
	    {
		foreach ($routes as $route) {
		    /* @var $route Inside_Analytics_Model_Route */
		    if ($route->matches(
			    $requestArray[Inside_Analytics_Helper_Data::REQUEST_PART_MODULE],
			    $requestArray[Inside_Analytics_Helper_Data::REQUEST_PART_CONTROLLER],
			    $requestArray[Inside_Analytics_Helper_Data::REQUEST_PART_ACTION]
			)) 
		    {
			$match = $type; break;
		    }		    	    
		}
		if ($match) {
		    break;
		}
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
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	foreach ($this->getTypeActions() as $type => $routes)
	{
	    foreach ($routes as $route) {
		/* @var $route Inside_Analytics_Model_Route */
		if ($route->getFullQualifier() == $key) {
		    return $type;
		}
	    }
	}
	return false;
    }
    
    /**
     * Get page name based on it's type; defaults to page title
     * 
     * @param string $type
     * @return string
     */
    public function getPageName($type) 
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
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
		$name = Mage::helper('inside')->getPageTitle(); break;
	}
	return $name;
    }
}