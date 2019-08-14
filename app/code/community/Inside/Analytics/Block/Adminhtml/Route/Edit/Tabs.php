<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Block_Adminhtml_Route_Edit_Tabs 
    extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('inside_route_edit_tabs');
        $this->setDestElementId('edit_form');
    }

}
