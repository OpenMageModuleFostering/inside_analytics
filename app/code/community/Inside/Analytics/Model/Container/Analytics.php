<?php

/**
 * Special thanks to Alan Taylor for this implementation.
 * 
 * @category    Inside
 * @package     Inside_Analytics
 * @author      Inside <martin.novak@inside.tm>
 */
class Inside_Analytics_Model_Container_Analytics extends Enterprise_PageCache_Model_Container_Abstract
{
    
    protected function _getCacheId()
    {
	$key  = $this->_placeholder->getAttribute('cache_id');
	return 'INSIDE_ANALYTICS_' . md5($key);
    }

    protected function _renderBlock()
    {
	$blockClass = $this->_placeholder->getAttribute('block');
	$template = $this->_placeholder->getAttribute('template');
	$block = new $blockClass;
	$block->setTemplate($template);
	$block->setLayout(Mage::app()->getLayout());
	return $block->toHtml();
    }
    
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
	return false;
    }
}
