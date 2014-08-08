<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 * @info			http://fishpig.co.uk/wordpress-integration.html
 */
 /**
  * Ugly Hacks
  *
  * The code below adds support for Magento 1.5.
  *
  */
  
	$basePath = DS . implode(DS, array_slice(explode('/', trim(dirname(__FILE__), DS)), 0, -3)) 
		. DS . implode(DS, array('core', 'Mage', 'Model'));
	

	// Resource DB File hack	
	$resourceDb = $basePath . DS . implode(DS, array('Resource', 'Db', 'Abstract.php'));

	if (!is_file($resourceDb)) {
		abstract class Mage_Core_Model_Resource_Db_Abstract extends Mage_Core_Model_Mysql4_Abstract {}
	}

	// Resource DB File hack	
	$resourceDbCollection = $basePath . DS . implode(DS, array('Resource', 'Db', 'Collection', 'Abstract.php'));

	if (!is_file($resourceDbCollection)) {
		abstract class Mage_Core_Model_Resource_Db_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract {}
	}
