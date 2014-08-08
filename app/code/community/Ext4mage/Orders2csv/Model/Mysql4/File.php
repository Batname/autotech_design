<?php
/**
* Ext4mage Orders2csv Module
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to Henrik Kier <info@ext4mage.com> so we can send you a copy immediately.
*
* @category   Ext4mage
* @package    Ext4mage_Orders2csv
* @copyright  Copyright (c) 2012 Ext4mage (http://ext4mage.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @author     Henrik Kier <info@ext4mage.com>
* */
class Ext4mage_Orders2csv_Model_Mysql4_File extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		// Note that the orders2csv_id refers to the key field in your database table.
		$this->_init('orders2csv/file', 'file_id');
		
	}

	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if (! $object->getId()) {
			$object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
		}
		$object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
		return $this;
	}

	public function getFileByActive (){
		$objects = array();
		 
		$select = $this->_getReadAdapter()->select();
		 
		$select->from($this->getMainTable());
		$select->where("is_active = 1");
		$select->order('title DESC');

		return $this->_getReadAdapter()->fetchAll($select);
	}
}