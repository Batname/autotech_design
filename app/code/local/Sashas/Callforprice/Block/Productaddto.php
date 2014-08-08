<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Productaddto extends Mage_Catalog_Block_Product_View
{
	public function _prepareLayout()
	{
		$this->setProduct( Mage::registry('current_product'));
		$this->setTemplate('callforprice/callforprice_product_addtocart.phtml');	
		return parent::_prepareLayout();
	}	
	
	public function GetGroupId() {
		$customer_group=0;
		if (Mage::helper('customer')->isLoggedIn()) {
			$customer_group=Mage::helper('customer')->getCustomer()->getGroupId();
		}
	
		return $customer_group;
	}	
}