<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Block_Adminhtml_Route_Grid 
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
	$collection = Mage::getResourceModel('inside/route_collection');
        /* @var $collection Inside_Analytics_Model_Mysql4_Route_Collection */
	
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
	$this->addColumn('is_active', array(
	    'header' => $this->__('Status'),
	    'width'  => '80px',
	    'type'   => 'options',
	    'options'=> array(
		0 => 'Disabled',
		1 => 'Enabled',
	    ),
	    'index'  => 'is_active',
	));
	
        $this->addColumn('module', array(
            'header' => $this->__('Module Name'),
	    'width' => '100px',
            'index' => 'module',
        ));

        $this->addColumn('controller', array(
            'header' => $this->__('Controller Name'),
	    'width' => '160px',
            'index' => 'controller',
        ));
	
	$this->addColumn('action_name', array(
	    'header' => $this->__('Action Name'),
	    'width'  => '160px',
	    'index'  => 'action',
	));
        
        $this->addColumn('full_qualifier', array(
            'header' => $this->__('Full Route'),
	    'width'  => '160px',
            'index' => 'full_qualifier',
        ));
	
	$this->addColumn('type', array(
	    'header' => $this->__('Page Type'),
	    'index'  => 'type',
	    'type'   => 'options',
	    'options'=> Mage::getSingleton('inside/system_config_source_page_type')->getOptions(),
	));
	
	$this->addColumn('is_ajax', array(
	    'header' => $this->__('Is Ajax'),
	    'width'  => '80px',
	    'type'   => 'options',
	    'options'=> array(
		0 => 'No',
		1 => 'Yes',
	    ),
	    'index'  => 'is_ajax',
	));
	
	$this->addColumn('user_defined', array(
	    'header' => $this->__('User Defined'),
	    'width'  => '80px',
	    'type'   => 'options',
	    'options'=> array(
		0 => 'No',
		1 => 'Yes',
	    ),
	    'index'  => 'user_defined',
	));
        
        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                'view' => array(
                    'caption' => $this->__('View'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction() {
	parent::_prepareMassaction();
	
	$this->getMassactionBlock()->addItem('enable_all', array(
            'label'=> Mage::helper('inside')->__('Enable Routes'),
            'url'  => $this->getUrl('*/*/massEnable'),
        ));
	
	$this->getMassactionBlock()->addItem('disable_all', array(
            'label'=> Mage::helper('inside')->__('Disable Routes'),
            'url'  => $this->getUrl('*/*/massDisable'),
        ));
	
	$this->getMassactionBlock()->addItem('delete_all', array(
            'label'=> Mage::helper('inside')->__('Delete Routes'),
            'url'  => $this->getUrl('*/*/massDelete'),
	    'confirm' => Mage::helper('inside')->__('Only user defined routes can be deleted. Delete selected routes?'),
        ));
	
	$this->setMassactionIdField('id');	
	$this->getMassactionBlock()->setUseSelectAll(false);

        return $this;
    }    
   

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}

