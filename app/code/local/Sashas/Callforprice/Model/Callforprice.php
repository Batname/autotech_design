<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Model_Callforprice extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('callforprice/callforprice');
	}
	
	public function loadByProductId($product_id)
	{
		$data= $this->_getResource()->loadByProductId($product_id);
		$this->setData($data);
		return $this;
	}
	 
}