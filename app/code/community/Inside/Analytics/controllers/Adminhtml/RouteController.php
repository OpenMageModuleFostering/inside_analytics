<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Adminhtml_RouteController extends Mage_Adminhtml_Controller_Action {
    
    const ERR_UNIQUE_CONSTRAINT = 'Track route with the same name already exists.';
    const ERR_SAVE_GENERIC = 'Error saving track route name.';
    
    const MODE_DISABLE = 0;
    const MODE_ENABLE = 1;
    const MODE_DELETE = 2;
    
    
    protected $_allowedModes = array(
	self::MODE_DISABLE => 'disabled',
	self::MODE_ENABLE => 'enabled',
	self::MODE_DELETE => 'deleted',
    );
    
    public function indexAction()
    {
	$this->loadLayout()->_setActiveMenu('inside/route')->renderLayout();
    }
    
    public function newAction() {
	$this->_forward('edit');
    }
    
    public function editAction()
    {
	$id = $this->getRequest()->getParam('id', null);
	$model = Mage::getModel('inside/route');
	/* @var $model Inside_Analytics_Model_Route */
	if ($id) {
	    $model->load((int) $id);
	    if ($model->getId()) {
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if ($data) {
		    $model->setData($data)->setId($id);
		}
	    } else {
		Mage::getSingleton('adminhtml/session')->addError($this->__('Route does not exist'));
		$this->_redirect('*/*/');
	    }
	}
	Mage::register('current_inside_route', $model);

	$this->loadLayout();
	$this->renderLayout();
    }
    
    public function saveAction()
    {
	if ($data = $this->getRequest()->getPost()) {
	    $model = Mage::getModel('inside/route');
	    /* @var $model Inside_Analytics_Model_Route */
	    $id = $this->getRequest()->getParam('id');
	    foreach ($data as $key => $val) {	
		$data[$key] = trim($val);
	    }
	    $model->setData($data);
	    Mage::getSingleton('adminhtml/session')->setFormData($data);
	    try {
		if ($id) {
		    $model->setId($id);
		}
		
		$model->save();	
		if (!$model->getId()) {
		    Mage::throwException($this->__('Error saving route'));
		}
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Route was successfully saved.'));
		Mage::getSingleton('adminhtml/session')->setFormData(false);

		$this->_redirect('*/*/');
	    } catch (Exception $e) {
		Mage::logException($e);
		if(stristr($e->getMessage(), 'UNIQUE_ROUTE_NAME')) {
		    Mage::getSingleton('adminhtml/session')->addError(self::ERR_UNIQUE_CONSTRAINT);
		} else {
		    Mage::getSingleton('adminhtml/session')->addError(self::ERR_SAVE_GENERIC);
		}
		if ($model && $model->getId()) {
		    $this->_redirect('*/*/edit', array('id' => $model->getId()));
		} else {
		    $this->_redirect('*/*/');
		}
	    }

	    return;
	}
	Mage::getSingleton('adminhtml/session')->addError($this->__('No data found to save'));
	$this->_redirect('*/*/');
    }
    
    public function deleteAction() {
	$id = $this->getRequest()->getParam('id');
	$model = Mage::getModel('inside/route')->load($id);

	if ($model->getId() == $id) {
	    try {
		$model->delete();
		$this->_getSession()->addSuccess($this->__('The route has been deleted.'));
		$this->_redirect('*/*/');
		return;
	    } catch (Exception $e) {
		$this->_getSession()->addError($this->__('Error deleting route.'));
		$this->_redirect('*/*/edit', array('id' => $id));
		return;
	    }
	} else {
	    $this->_getSession()->addError($this->__('Invalid route id supplied. Route does not exist.'));
	    $this->_redirect('*/*/');
	    return;
	}
    }
    
    public function massDeleteAction() {
	return $this->massStatusAction(self::MODE_DELETE);
    }
    
    public function massEnableAction() {
	return $this->massStatusAction(self::MODE_ENABLE);
    }
    
    public function massDisableAction() {
	return $this->massStatusAction(self::MODE_DISABLE);
    }
    
    protected function massStatusAction($mode = 0)
    {
	if(!array_key_exists($mode, $this->_allowedModes)) {
	    $this->_getSession()->addError('Invalid mode specified for mass status action.');
	    $this->_redirect('*/*');
	    return;
	}	
	$params = $this->getRequest()->getParams();
	if (!isset($params['massaction']) || !is_array($params['massaction']) || empty($params['massaction'])) {
	    $this->_getSession()->addError(Mage::helper('inside')->__('No routes selected.'));
	    $this->_redirect('*/*/');
	}
	$route_ids = $params['massaction'];
	$notices = array(); $count = 0;
	foreach ($route_ids as $id) {
	    $route = Mage::getModel('inside/route')->load($id);
	    /* @var $route Inside_Analytics_Model_Route */
	    if (!$route || !$route->getId()) {
		$notices[] = "Route ID $id not found.";
		continue;
	    }	    
	    switch($mode) {
		case self::MODE_DELETE:
		    if ($route->getUserDefined()) {
			$route->delete();
		    } else {
			$count--;
		    }
		    break;
		case self::MODE_ENABLE:
		case self::MODE_DISABLE:
		    $route->setIsActive($mode);
		    $route->save();
		    break;
	    }
	    
	    $count++;
	}
	if (!empty($notices)) {
	    foreach ($notices as $notice) {
		$this->_getSession()->addError($notice);
	    }
	}	
	$successMsg = $this->_allowedModes[$mode];
	$this->_getSession()->addSuccess("Total of $count routes $successMsg.");
	//back to index route grid
	$this->_redirect('*/*/');

	return;
    }
    
}
