<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Model_Observer {
 
    public function setOrderSuccessPageView(Varien_Event_Observer $observer)
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);	
	$orderIds = $observer->getEvent()->getOrderIds();
	Mage::helper('inside')->log('$orderIds: '.$orderIds, true);
        
	if (empty($orderIds) || !is_array($orderIds)) {
	    Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('inside_analytics');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
	
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
    }
    
    /**
     * Saves update on next flag into session (Ajax calls only)
     * 
     * @param Varien_Event_Observer $observer
     */
    public function setAjax(Varien_Event_Observer $observer)
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$frontController = $observer->getEvent()->getFront();
	/* @var $frontController Mage_Core_Controller_Varien_Front */
	$routes = Mage::getResourceModel('inside/route_collection')
			->addFieldToFilter('is_active', 1)
			->addFieldToFilter('is_ajax', 1);
	Mage::helper('inside')->log('# OF ROUTES: '.$routes->count(), true);
	foreach ($routes as $route) {
	    /* @var $route Inside_Analytics_Model_Route */
	    if ($route->matches(
		    $frontController->getRequest()->getModuleName(),
		    $frontController->getRequest()->getControllerName(),
		    $frontController->getRequest()->getActionName()
		)) {
		Mage::helper('inside')->log('Route ID '. $route->getId() . ' AJAX REQUEST MATCH --> setting ajax session variable.', true);
		Mage::getSingleton('core/session')->setInsideUpdateOnNext(true);
	    } else {
		Mage::helper('inside')->log('Route ID '. $route->getId() . ' AJAX REQUEST DOES NOT MATCH', true);
	    }
	}
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
    }
}

