<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Block_Adminhtml_Route 
    extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
    public function __construct()
    {
        $this->_blockGroup = 'inside';
        $this->_controller = 'adminhtml_route';
        $this->_headerText = $this->__('Track Routes');
	$this->_addButtonLabel = 'Add New Route';
        parent::__construct();
	
    }
}

