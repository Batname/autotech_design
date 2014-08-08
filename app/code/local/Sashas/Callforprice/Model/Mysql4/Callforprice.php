<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Model_Mysql4_Callforprice extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		//parent::_construct();
		$this->_init('callforprice/callforprice','value_id');
	}
	
 
	public function loadByProductId($product_id)
	{
		$adapter = $this->_getReadAdapter();
	
		$select = $adapter->select()
		->from(Mage::getConfig()->getTablePrefix().'sashas_callforprice_product', '*')
		->where('product_id ='.$product_id);	
		  	
		return $adapter->fetchRow($select);
	}
}