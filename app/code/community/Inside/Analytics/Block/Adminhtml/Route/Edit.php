<?php
/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Block_Adminhtml_Route_Edit 
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * The route being edited
     * @var Inside_Analytics_Model_Route
     */
    protected $_route = null;
    
    public function __construct()
    {
	parent::__construct();

	$this->_objectId = 'id';
        $this->_blockGroup = 'inside';
        $this->_controller = 'adminhtml_route';
	
	if ($this->getRoute() && $this->getRoute()->getId() && 
		!$this->getRoute()->getUserDefined()) 
	{
	    //system entry - read only
	    $this->_removeButton('delete');
	} 

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
    
    
    public function getHeaderText()
    {
        if ($this->getRoute() && $this->getRoute()->getId()) {
            return $foo = $this->__(
                'Route # %s | %s',
                $this->htmlEscape($this->getRoute()->getId()),
                $this->htmlEscape($this->getRoute()->getFullQualifier())
            );
        } else {
	    return $foo = $this->__('New Track Route');
	}
    }
}