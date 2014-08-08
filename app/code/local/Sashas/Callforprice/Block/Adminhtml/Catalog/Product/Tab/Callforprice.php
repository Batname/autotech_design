<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Adminhtml_Catalog_Product_Tab_Callforprice extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm()
	{
		$product_id = $this->getProductId();
		 
		$model=Mage::getModel('callforprice/callforprice')->loadByProductId($product_id);
		
		$cfp_text=( ($model->getValue()) ? $model->getValue() : 'Call for price.' ) ;
		$cfp_status=( ($model->getStatus()) ? $model->getStatus() : 0 ) ;
		$cfp_addtocart=( ($model->getAddtocartEnabled()) ? $model->getAddtocartEnabled() : 0 ) ;	
		$excluded_customer_groups=$model->getCustomerGroups();
		$groups=Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
		$no_option=array( 'label' => 'Please Select..', 'value' => '-1');
		array_unshift($groups, $no_option);
		
		$form = new Varien_Data_Form();
		$fieldset=$form->addFieldset('callforprice_options', array('legend'=>Mage::helper('callforprice')->__('Call For Price Module options')));
 
		$fieldset->addField('callforprice_text', 'text', array(
				'label'=> Mage::helper('catalog')->__('Call for Price Text:'),
				'name'=>'callforprice_text',				 
				'value'=>$cfp_text
		));
		
		$fieldset->addField('callforprice_enabled', 'select', array(
				'name'=>'callforprice_enabled',
				'label'=>'Enable Extension:',
				'values'=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
				'value'=>$cfp_status 
		));
		
		$fieldset->addField('callforprice_addtocart', 'select', array(
				'name'=>'callforprice_addtocart',
				'label'=>'Show Add to Cart button:',
				'values'=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
				'value'=>$cfp_addtocart
		));
		
		$fieldset->addField('callforprice_excluded_customergroups', 'multiselect', array(
				'name'=>'callforprice_excluded_customergroups[]',
				'label'=>'Ignore for customer groups:',	
				'value'=> $excluded_customer_groups,
				'values'=> $groups  
		));		
		
		$form->getElement('callforprice_enabled')->setIsChecked($cfp_status);
		$form->getElement('callforprice_addtocart')->setIsChecked($cfp_addtocart);
		if (!$cfp_status) {
			$form->getElement('callforprice_text')->setDisabled(true, true);
		}
	 		
		$this->setForm($form);
	}
	
}