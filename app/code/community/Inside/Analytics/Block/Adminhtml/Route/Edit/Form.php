<?php
/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Block_Adminhtml_Route_Edit_Form 
    extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
	parent::__construct();
	$this->setId('route_form');
    }
    
    protected function _prepareForm()
    {
	$form = new Varien_Data_Form(
		array(
		    'id' => 'edit_form', 
		    'action' => $this->getData('action'), 
		    'method' => 'post')
	);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}