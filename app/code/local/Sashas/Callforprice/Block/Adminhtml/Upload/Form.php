<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Adminhtml_Upload_Form extends Mage_Adminhtml_Block_Widget_Form{

 
	/* (non-PHPdoc)
	 * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
	 */
	protected function _prepareForm(){
		 
        $form = new Varien_Data_Form(array(
            'id'        => 'callforprice_upload_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));
        
        $fieldset = $form->addFieldset('callforprice_upload_form', array(
        		'legend' =>Mage::helper('callforprice')->__('Excel .csv File')
        ));
        
         $fieldset->addField('filecsv', 'file', array(
        		'label'     => Mage::helper('callforprice')->__('Upload'),
        		'value'  => 'Upload',
        		'disabled' => false,
        		'readonly' => true,
         		'name'=>'filecsv', 
         		'required'=>true,
        		'after_element_html' => '<small>Please select .csv file with values</small>',
        		'tabindex' => 1
        )); 
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
	
}