<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Model_Observer
{
    static protected $_singletonFlag = false;
 
    public function saveProductTabData(Varien_Event_Observer $observer)
    {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;
           $product = $observer->getEvent()->getProduct();
           try {
  			 
                $cfp_text =  $this->_getRequest()->getPost('callforprice_text');
                $cfp_enabled = ( ($this->_getRequest()->getPost('callforprice_enabled')) ? $this->_getRequest()->getPost('callforprice_enabled') : 0 );
                $cfp_addtocart_enabled =  (($this->_getRequest()->getPost('callforprice_addtocart')) ? $this->_getRequest()->getPost('callforprice_addtocart') : 0 );
				$excluded_customer_groups= $this->_getRequest()->getPost('callforprice_excluded_customergroups');
               		
                /*
                if (!$cfp_enabled)
                	$cfp_enabled=0;
                
                if (!$cfp_addtocart_enabled)
                	$cfp_addtocart_enabled=0;                               
                 */
				
                	$product_id= $product->getId();                  	         	
                	$model=Mage::getModel('callforprice/callforprice')->loadByProductId($product_id);
                	
                	if (!$model->getId()) {
                		$model->setProductId($product_id);
                	}
                	                	
                	$model->setAddtocartEnabled($cfp_addtocart_enabled);
                	$model->setStatus($cfp_enabled);
                	$model->setCustomerGroups(implode(',',$excluded_customer_groups));
                 
                	if (isset($cfp_text)) 
                		$model->setValue($cfp_text);
                	$model->save();
                              
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
 
    public function getProduct()
    {
        return Mage::registry('product');
    }
 
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
    
    public function addMassAction($observer)
    {
    	$block = $observer->getEvent()->getBlock();
    	if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
    			&& $block->getRequest()->getControllerName() == 'catalog_product')
    	{
    		$block->addItem('callforprice', array(
    				'label' => 'Update Call For Price Values',
    				'url' => Mage::app()->getStore()->getUrl('callforprice/admintab/massupdate'),
    		));
    	}
    }
}