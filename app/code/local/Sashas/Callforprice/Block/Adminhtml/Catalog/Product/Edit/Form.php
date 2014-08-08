<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Adminhtml_Catalog_Product_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	
	protected function _prepareForm(){
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array()),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));
       
        $product_ids=$this->getRequest()->getParam('product');
        $excluded_customer_groups='-1';
        $groups=Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
        $no_option=array( 'label' => 'Please Select..', 'value' => '-1');
        array_unshift($groups, $no_option);
        
        $fieldset=$form->addFieldset('callforprice_options', array('legend'=>Mage::helper('callforprice')->__('Call For Price Module options')));
        
        $element =$fieldset->addField('callforprice_text', 'text', array(
        		'label'=> Mage::helper('catalog')->__('Call for Price Text:'),
        		'name'=>'callforprice_text',
        		'value'=>''
        ));
        $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
       	$element = $fieldset->addField('callforprice_enabled', 'select', array(
        		'name'=>'callforprice_enabled',
        		'label'=>'Enable Extension:',        		
       			'values'=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        		'value'=>''
        ));
        $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
       
        $element = $fieldset->addField('callforprice_addtocart', 'select', array(
        		'name'=>'callforprice_addtocart',
        		'label'=>'Show Add to Cart button:',
        		'values'=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        		'value'=>'',
        ));               
        $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
        
        $element =$fieldset->addField('callforprice_excluded_customergroups', 'multiselect', array(
        		'name'=>'callforprice_excluded_customergroups[]',
        		'label'=>'Ignore for customer groups:',
        		'value'=> $excluded_customer_groups,
        		'values'=> $groups
        ));
        $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
                
        $fieldset->addField('products_id', 'text', array(   
        		'style'=>'display:none',     
        		'name'=>'products_id',
        		'value'=> implode(',', $product_ids)
        ));
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    protected function _getAdditionalElementHtml($element)
    {
    	return '<span class="attribute-change-checkbox"><input type="checkbox" id="' . $element->getId()
    	. '-checkbox" onclick="toogleFieldEditMode(this, \'' . $element->getId()
    	. '\')" /><label for="' . $element->getId() . '-checkbox">' . Mage::helper('catalog')->__('Change')
    	. '</label></span>
    	<script type="text/javascript">initDisableFields(\''.$element->getId().'\')</script>';
    }
    
	
}
