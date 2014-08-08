<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	/**
	 * @param (string) $file
	 */
	public function processfile($file){
		$row = 0;
		if (($handle = fopen($file, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				if ($row==0) {
					$row++;
					continue;
				}
				$sku = trim($data[0]);
				$callforprice_enabled=trim($data[1]);
				$callforprice_addtocart_enabled=trim($data[2]);
				$callforprice_text=trim($data[3]);
				$excluded_customer_groups=trim($data[4]);
				
				if (!$callforprice_enabled)
					$callforprice_enabled=0;
				if (!$callforprice_addtocart_enabled)
					$callforprice_addtocart_enabled=0;
				
				$_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
				
				if (!$_product || !$_product->getId())  {
					throw new Exception("Product ".$sku." doesn't found. Please check if this sku correct or product exists.");
					continue;
				}
				
				$product_id= $_product->getId();
				$model=Mage::getModel('callforprice/callforprice')->loadByProductId($product_id);
				 
				if (!$model->getId()) {
					$model->setProductId($product_id);
				}
				
				$model->setAddtocartEnabled($callforprice_addtocart_enabled);
				$model->setStatus($callforprice_enabled);				 
				$model->setValue($callforprice_text);
				$model->setCustomerGroups($excluded_customer_groups);
				$model->save();
				
			}
		}
		fclose($handle);
		unlink($file);
	}
}