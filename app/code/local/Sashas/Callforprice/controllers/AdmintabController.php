<?php

/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_AdmintabController extends Mage_Adminhtml_Controller_Action {
	
	public function getoptionsAction(){			 
	  $id = (int) $this->getRequest()->getParam('id');
	  $this->getResponse()->setBody(
	  $this->getLayout()->createBlock('callforprice/adminhtml_catalog_product_tab_callforprice') 
		    ->setProductId($id) 
		    ->setUseAjax(true)
		   ->toHtml()
		);
	}
	
	public function massupdateAction(){
		
		$this->loadLayout()->_setActiveMenu('catalog');
		$this->_addContent($this->getLayout()->createBlock('callforprice/adminhtml_catalog_product_edit'))
		->_addLeft($this->getLayout()->createBlock('callforprice/adminhtml_catalog_product_edit_tabs'));
        $this->renderLayout();
	}
	
	public function saveAction(){
		if ( $this->getRequest()->getPost() ) {
			try {
				$product_ids=explode(',', $this->getRequest()->getParam('products_id'));
				$cfp_value=$this->getRequest()->getParam('callforprice_text');
				$cfp_status=$this->getRequest()->getParam('callforprice_enabled');
				$cfp_addtocard=$this->getRequest()->getParam('callforprice_addtocart');
				$excluded_customer_groups= $this->getRequest()->getParam('callforprice_excluded_customergroups');
								 				
				foreach ($product_ids as $product_id){			
					$model=Mage::getModel('callforprice/callforprice')->loadByProductId($product_id);
			 
					if (!$model->getId()) {
						$model->setProductId($product_id);
					}		
					if ($excluded_customer_groups)
					$model->setCustomerGroups(implode(',',$excluded_customer_groups));
					if ($cfp_addtocard)
					$model->setAddtocartEnabled($cfp_addtocard);
					if ($cfp_status)
					$model->setStatus($cfp_status);
					if ($cfp_value)
					$model->setValue($cfp_value);
					$model->save();
				}
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setCallforpriceData($this->getRequest()->getPost());
				$this->_redirect('*/*/massupdate');
				return;
			}
		}
		 
		Mage::getSingleton('adminhtml/session')->addSuccess('Changes was made. Thank you.');
		$this->_redirect("adminhtml/catalog_product");
	}
}