<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sidebar_Widget_Yarpp extends Mage_Core_Block_Template
{
	/**
	 * This block is deprecated and will no longer work
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		$this->setTemplate(null);
		
		return parent::_beforeToHtml();
	}
}
