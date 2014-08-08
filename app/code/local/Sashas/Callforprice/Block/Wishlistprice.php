<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Wishlistprice extends Mage_Wishlist_Block_Customer_Sidebar
{
	public function __construct()
	{
		parent::__construct(); 
		$this->setTemplate('callforprice/listprice.phtml');
	}
	
	public function GetGroupId() {
		$customer_group=0;
		if (Mage::helper('customer')->isLoggedIn()) {
			$customer_group=Mage::helper('customer')->getCustomer()->getGroupId();
		}
		
		return $customer_group;
	}
	
}