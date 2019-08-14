<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Model_Mysql4_Route_Collection extends
    Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
	$this->_init('inside/route');
    }
}
