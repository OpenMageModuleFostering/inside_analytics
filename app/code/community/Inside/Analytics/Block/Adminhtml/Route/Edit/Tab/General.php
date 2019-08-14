<?php
/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Block_Adminhtml_Route_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * The route being edited
     * @var Inside_Analytics_Model_Route
     */
    protected $_route = null;
    
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Track Route Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Track Route Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_inside_route');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('route_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => $this->__('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }
	
	$fieldset->addField('is_active', 'select', array(
	    'name' => 'is_active',
	    'label' => $this->__('Status'),
	    'title' => $this->__('Status'),
	    'required' => true,
	    'options' => array(
                '1' => $this->__('Enabled'),
                '0' => $this->__('Disabled'),
            ),
	));

        $fieldset->addField('module', 'text', array(
            'name' => 'module',
            'label' => $this->__('Module Name'),
            'title' => $this->__('Module Name'),
            'required' => true,
	    'disabled' => $this->isReadOnly(),
	    'note' => $this->__('The modules route name (\'frontName\' as specified in modules configuration file).'),
        ));
	
	$fieldset->addField('controller', 'text', array(
            'name' => 'controller',
            'label' => $this->__('Controller Name'),
            'title' => $this->__('Controller Name'),
            'required' => false,
	    'disabled' => $this->isReadOnly(),
	    'note' => $this->__('Leave blank for any.'),
        ));
	
	$fieldset->addField('action', 'text', array(
            'label'     => $this->__('Action Name'),
            'title'     => $this->__('Action Name'),
            'name'      => 'action',
            'required' => false,
	    'disabled' => $this->isReadOnly(),
	    'note' => $this->__('Leave blank for any. Only applicable if controller name exists.'),
        ));
	
	$fieldset->addField('type', 'select', array(
	    'name' => 'type',
	    'label' => $this->__('Page Type'),
	    'title' => $this->__('Page Type'),
	    'required' => true,
	    'disabled' => $this->isReadOnly(),
	    'options' => Mage::getSingleton('inside/system_config_source_page_type')->getOptions(),
	));
	
	$fieldset->addField('is_ajax', 'select', array(
	    'name' => 'is_ajax',
	    'label' => $this->__('Is Ajax'),
	    'title' => $this->__('Is Ajax'),
	    'required' => true,
	    'disabled' => $this->isReadOnly(),
	    'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
	));
	
	$fieldset->addField('search_param', 'text', array(
            'label'     => $this->__('Search Param'),
            'title'     => $this->__('Search Param'),
            'name'      => 'search_param',
            'required' => true,
	    'disabled' => $this->isReadOnly(),
	    'note' => $this->__('The search parameter name.'),
        ));
	
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Gets the route being edited.
     *
     * @return Inside_Analytics_Model_Route
     */
    public function getRoute()
    {
        if (!$this->_route) {
            $this->_route = Mage::registry('current_inside_route');
        }
        return $this->_route;
    }
    
    /**
     * Is the current route read only (system) route?
     * @return boolean
     */
    public function isReadOnly()
    {
	if ($this->getRoute() && $this->getRoute()->getId() && 
		!$this->getRoute()->getUserDefined())
	{
	    return true;
	}
	return false;
    }
}
