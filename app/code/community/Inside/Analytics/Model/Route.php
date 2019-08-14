<?php

/**
 * @method string getModule()
 * @method string getController()
 * @method string getAction()
 * @method string getFullQualifier()
 * @method string getType()
 * @method boolean getUserDefined()
 * 
 * @method Inside_Analytics_Model_Route setModule()
 * @method Inside_Analytics_Model_Route setController()
 * @method Inside_Analytics_Model_Route setAction()
 * @method Inside_Analytics_Model_Route setFullQualifier()
 * @method Inside_Analytics_Model_Route setType()
 * @method Inside_Analytics_Model_Route setUserDefined()
 * 
 * 
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Model_Route extends Mage_Core_Model_Abstract {
    
    public function _construct()
    {
	parent::_construct();
	$this->_init('inside/route');
    }
    
    /**
     * Checks if request matches this tracking route
     * 
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function matches($module, $controller, $action)
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	Mage::helper('inside')->log('$module: '.$module, true);
	Mage::helper('inside')->log('$this->getModule(): '.$this->getModule(), true);
	if ($this->getModule() !== $module) {
	    Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	    return false;
	}
	Mage::helper('inside')->log('$controller: '.$controller, true);
	Mage::helper('inside')->log('$this->getController(): '.$this->getController(), true);
	if ($this->getController() && $this->getController() !== $controller) {
	    Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	    return false;
	}
	Mage::helper('inside')->log('$action: '.$action, true);
	Mage::helper('inside')->log('$this->getAction(): '.$this->getAction(), true);
	if ($this->getAction() && $this->getAction() !== $action) {
	    Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	    return false;
	}
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	return true;
    }
    
    /**
     * Fixes full route qualifier
     * @return \Inside_Analytics_Model_Route
     */
    protected function _fixRouteName()
    {
	Mage::helper('inside')->log('ENTERING: '.__METHOD__, true);
	
	$fullName = $this->getModule();
	if ($this->getController()) {
	    $fullName = $fullName . '_' . $this->getController();
	    
	    if ($this->getAction()) {
		$fullName = $fullName . '_' . $this->getAction();
	    }
	}
	$this->setFullQualifier($fullName);
	Mage::helper('inside')->log('LEAVING: '.__METHOD__, true);
	return $this;
    }
    
    protected function _beforeSave()
    {
	parent::_beforeSave();
	$this->_fixRouteName();
	return $this;
    }
}
