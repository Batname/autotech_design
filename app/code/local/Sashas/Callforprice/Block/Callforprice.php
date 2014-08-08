<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Callforprice extends Mage_Catalog_Block_Product_View
{

	public function _prepareLayout()	
	{	
		$product=Mage::registry('current_product');
	 	$product_type=$product->getTypeId(); 
	 	if ($product_type=="grouped") {
	 		$this->setTemplate('callforprice/callforprice_grouped.phtml');
	 	} else {
	 		$this->setTemplate('callforprice/callforprice_simple.phtml');
	 	}
		
		return parent::_prepareLayout();	
	}

	public function getProduct()
	{		 
		if (!Mage::registry('current_product') && $this->getProductId()) {
			$product = Mage::getModel('catalog/product')->load($this->getProductId());
			Mage::register('current_product', $product);
		}
		return Mage::registry('product');
	}
	
	public function getAssociatedProducts()
	{
		return $this->getProduct()->getTypeInstance(true)
		->getAssociatedProducts($this->getProduct());
	}
	
	
	/**
	 * Set preconfigured values to grouped associated products
	 *
	 * @return Mage_Catalog_Block_Product_View_Type_Grouped
	 */
	public function setPreconfiguredValue() {
		$configValues = $this->getProduct()->getPreconfiguredValues()->getSuperGroup();
		if (is_array($configValues)) {
			$associatedProducts = $this->getAssociatedProducts();
			foreach ($associatedProducts as $item) {
				if (isset($configValues[$item->getId()])) {
					$item->setQty($configValues[$item->getId()]);
				}
			}
		}
		return $this;
	}
	
	public function getCanShowProductPrice($product)
	{
		return $product->getCanShowPrice() !== false;
	}
	
	public function GetGroupId() {
		$customer_group=0;
		if (Mage::helper('customer')->isLoggedIn()) {
			$customer_group=Mage::helper('customer')->getCustomer()->getGroupId();
		}
	
		return $customer_group;
	}
	
	
}