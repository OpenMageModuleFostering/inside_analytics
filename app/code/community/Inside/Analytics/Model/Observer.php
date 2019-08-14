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
	$orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('inside_analytics');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
    
    /**
     * Saves update on next flag into session (Ajax calls only)
     * 
     * @param Varien_Event_Observer $observer
     */
    public function setAjax(Varien_Event_Observer $observer)
    {
	$frontController = $observer->getEvent()->getFront();
	/* @var $frontController Mage_Core_Controller_Varien_Front */
	$routes = Mage::getResourceModel('inside/route_collection')
			->addFieldToFilter('is_active', 1)
			->addFieldToFilter('is_ajax', 1);
	
	foreach ($routes as $route) {
	    /* @var $route Inside_Analytics_Model_Route */
	    if ($route->matches(
		    $frontController->getRequest()->getModuleName(),
		    $frontController->getRequest()->getControllerName(),
		    $frontController->getRequest()->getActionName()
		)) {
		Mage::getSingleton('core/session')->setInsideUpdateOnNext(true);
	    }
	}
    }
}

