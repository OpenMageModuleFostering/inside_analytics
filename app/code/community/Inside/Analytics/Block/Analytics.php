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
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	Mage::helper('inside')->log('Page name:'.$this->_getData('page_name'), true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
        return $this->_getData('page_name');
    }
    
    /**
     * Get inside server name to connect to
     * 
     * @return string
     */
    protected function _getServer()
    {
	return Mage::helper('inside')->getServer();
    }
    
    /**
     * Get main getTracker code. This will initialise the _insideGraph tracker 
     * object with your account key.
     * 
     * @return string
     */
    protected function _getAccountCode()
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$accountId = Mage::getStoreConfig(Inside_Analytics_Helper_Data::XML_PATH_ACCOUNT);
	$visitorId = Mage::helper('inside')->getVisitorId();
	$visitorName = Mage::helper('inside')->getVisitorName();
	
	Mage::helper('inside')->log('$accountId:'.$accountId, true);
	Mage::helper('inside')->log('$visitorId:'.$visitorId, true);
	Mage::helper('inside')->log('$visitorName:'.$visitorName, true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
        
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
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$script = "_inside.push({";
	$data = Mage::getModel('inside/pageView')->getPageTrackCodeData($this->_requestArray);
	foreach ($data as $key => $val) {
	    if (is_null($val)) {
		continue;
	    }
	    $script .= '\''.$key.'\':'.  json_encode($val).',';
	}
	Mage::helper('inside')->log('$script: '.$script, true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	
	return substr($script, 0, strlen($script)-1) . "});";
    }

    /**
     * Render order items tracking code
     *
     * @return string
     */
    protected function _getOrdersTrackingCode()
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$script = '';
	$data = Mage::getModel('inside/pageView')->getOrderTrackCodeData($this->_requestArray);
	if (!empty($data)) {
	    foreach ($data as $index => $itemData) {
		$script .= "_inside.push({";
		foreach ($itemData as $key => $val) {
		    if (is_null($val)) {
			continue;
		    }
		    $script .= '\''.$key.'\':'.  json_encode($val).',';
		}
		$script = substr($script, 0, strlen($script)-1) . "});";
	    }
	}
	
	Mage::helper('inside')->log('$script: '.$script, true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	
	return $script;
    }
    
    /**
     * Render order complete tracking code
     * 
     * @return string
     */
    protected function _getSaleTrackingCode()
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
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
	
	Mage::helper('inside')->log('$script: '.$script, true);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	
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
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	parent::_prepareLayout();
	$action = Mage::app()->getFrontController()->getAction();
	
	if ($action) {
	    $this->_requestArray = array(
		'module'     => $action->getRequest()->getRequestedRouteName(),
		'controller' => $action->getRequest()->getRequestedControllerName(),
		'action'     => $action->getRequest()->getRequestedActionName()
	    );
	    Mage::helper('inside')->log('$this->_requestArray: '.print_r($this->_requestArray, true));
	}
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
    }
}
