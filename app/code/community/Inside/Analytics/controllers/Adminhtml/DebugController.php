<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Adminhtml_DebugController 
    extends Mage_Adminhtml_Controller_Action 
{
    public function showlogAction()
    {
	try {
	    $logDir  = Mage::getBaseDir('var') . DS . 'log';
            $logFile = $logDir . DS . Inside_Analytics_Helper_Data::LOG_FILENAME;

	    if (!is_dir($logDir)) {
		echo 'Log folder does not exist.';
		return;
	    }
	    if (!file_exists($logFile)) {
		echo 'Log file does not exist.';
		return;
	    }
	    $contents = file_get_contents($logFile);
	    echo nl2br($contents);
	} catch (Exception $ex) {
	    Mage::logException($ex);
	    echo 'Error occured when fetching Inside log file. See Magento exception log for more details.';
	}
    }
    
    public function clearlogAction()
    {
	$logFile = Mage::getBaseDir('var') . DS . 'log' . DS . Inside_Analytics_Helper_Data::LOG_FILENAME;
        if (file_exists($logFile)) {
	    try {
		if(unlink($logFile)) {
		    $this->_getSession()->addSuccess($this->__('Log file deleted.'));
		} else {
		    $this->_getSession()->addError($this->__('Unable to delete log file.'));
		}
	    } catch (Exception $ex) {
		Mage::logException($ex);
		$this->_getSession()->addError($this->__('Error occured when trying to delete Inside log file. See Magento exception log for more details.'));
	    }
	} else {
	    $this->_getSession()->addNotice($this->__('Inside log file does not exist.'));
	}
	$this->_redirect('adminhtml/system_config/edit/section/inside');
    }
}
