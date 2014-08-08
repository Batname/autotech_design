<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Resource_User_Collection extends Fishpig_Wordpress_Model_Resource_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/user');
	}
}
