<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Block_Analytics extends Mage_Core_Block_Template
{
    /**
     * The current request module, controller and action name
     * @var array
     */
    protected $_requestArray = null;
    
    /**
     * Get a specific page name (may be customized via layout)
     *
     * @return string|null
     */
    public function getPageName()
    {
        return $this->_getData('page_name');
    }
    
    /**
     * Get main getTracker code. This will initialise the _insideGraph tracker 
     * object with your account key.
     * 
     * @return string
     */
    protected function _getAccountCode()
    {
	$accountId = Mage::getStoreConfig(Inside_Analytics_Helper_Data::XML_PATH_ACCOUNT);
	$visitorId = Mage::helper('inside')->getVisitorId();
	$visitorName = Mage::helper('inside')->getVisitorName();
	return "_inside.push({
		    'action': 'getTracker', 'account': '{$this->jsQuoteEscape($accountId)}'{$visitorId}{$visitorName}
		});
	";
    }

    /**
     * Render regular page tracking javascript code
     *
     * @return string
     */
    protected function _getPageTrackingCode()
    {
	$script = "_inside.push({";
	$data = Mage::getModel('inside/pageView')->getPageTrackCodeData($this->_requestArray);
	foreach ($data as $key => $val) {
	    if (is_null($val)) {
		continue;
	    }
	    $script .= '\''.$key.'\':\''.  addslashes($val).'\',';
	}
	
	return substr($script, 0, strlen($script)-1) . "});";
    }

    /**
     * Render order items tracking code
     *
     * @return string
     */
    protected function _getOrdersTrackingCode()
    {
	$script = '';
	$data = Mage::getModel('inside/pageView')->getOrderTrackCodeData($this->_requestArray);
	if (!empty($data)) {
	    foreach ($data as $index => $itemData) {
		$script .= "_inside.push({";
		foreach ($itemData as $key => $val) {
		    if (is_null($val)) {
			continue;
		    }
		    $script .= '\''.$key.'\':\''.  addslashes($val).'\',';
		}
		$script = substr($script, 0, strlen($script)-1) . "});";
	    }
	}
	return $script;
    }
    
    /**
     * Render order complete tracking code
     * 
     * @return string
     */
    protected function _getSaleTrackingCode()
    {
	$orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
		->addFieldToFilter('entity_id', array('in' => $orderIds))
        ;
	$script = '';
        foreach ($collection as $order) {
	    /* @var $order Mage_Sales_Model_Order */
	    $script .= "_inside.push({
		'action':'trackOrder',
		'orderId':'{$order->getQuoteId()}',
		'orderTotal':'{$order->getGrandTotal()}',
		'complete':'true'});";
	}
	return $script;
    }
    
    /**
     * Render debug code - request parts
     * 
     * @return string
     */
    protected function _getDebugCode()
    {
	if (Mage::helper('inside')->canShowRequest()) {
	    return '<h3>'.implode('::', array_values($this->_requestArray)).'</h3>';
	}
	return '';
    }

    /**
     * Render Inside Analytics tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('inside')->isInsideAnalyticsAvailable() || !is_array($this->_requestArray)) {
	    return $this->_getDebugCode();
        }

        return parent::_toHtml();
    }
    
    /**
     * Load current request route params
     */
    protected function _prepareLayout() {
	parent::_prepareLayout();
	$action = Mage::app()->getFrontController()->getAction();
	if ($action) {
	    $this->_requestArray = array(
		'module'     => $action->getRequest()->getRequestedRouteName(),
		'controller' => $action->getRequest()->getRequestedControllerName(),
		'action'     => $action->getRequest()->getRequestedActionName()
	    );
	    if(Mage::helper('inside')->isLoggingEnabled()) {
		    Mage::log($this->_requestArray, null, 'inside-analytics.log', true);
	    }
	}
    }
}
