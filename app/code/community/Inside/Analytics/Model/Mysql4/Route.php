<?php

/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Model_Mysql4_Route 
    extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {
	$this->_init('inside/route', 'id');
    }
    
}
