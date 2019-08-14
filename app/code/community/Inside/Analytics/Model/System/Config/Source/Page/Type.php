<?php
/**
 * Description of class...
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Model_System_Config_Source_Page_Type 
    extends Inside_Analytics_Model_System_Config_Source_Abstract
{
    
    const HOMEPAGE	 = 'homepage';
    const ARTICLE	 = 'article';
    const SEARCH	 = 'search';
    const CATEGORY	 = 'productcategory';
    const PRODUCT	 = 'product';
    const LOGIN		 = 'login';
    const CHECKOUT	 = 'checkout';
    const ORDERCONFIRMED = 'orderconfirmed';
    const LEAD		 = 'lead';
    const LEADCONFIRMED  = 'leadconfirmed';
    const NOTFOUND	 = 'pagenotfound';
    const OTHER		 = 'other';
    
    protected function _setupOptions()
    {
        $this->_options = array(
            self::HOMEPAGE	    => Mage::helper('inside')->__('Home Page'),
	    self::ARTICLE	    => Mage::helper('inside')->__('Article Page'),
	    self::SEARCH	    => Mage::helper('inside')->__('Search Result Page'),
	    self::CATEGORY	    => Mage::helper('inside')->__('Product Category Page'),
	    self::PRODUCT	    => Mage::helper('inside')->__('Product View Page'),
	    self::LOGIN		    => Mage::helper('inside')->__('Login Page'),
	    self::CHECKOUT	    => Mage::helper('inside')->__('Shopping Cart or Checkout Page'),
	    self::ORDERCONFIRMED    => Mage::helper('inside')->__('Order Confirmation Page'),
//	    self::LEAD		    => Mage::helper('inside')->__('Lead/Form Page'),
//	    self::LEADCONFIRMED	    => Mage::helper('inside')->__('Lead/Form Confirmation Page'),
	    self::NOTFOUND	    => Mage::helper('inside')->__('Page not Found (404)'),
        );
    }
    
}