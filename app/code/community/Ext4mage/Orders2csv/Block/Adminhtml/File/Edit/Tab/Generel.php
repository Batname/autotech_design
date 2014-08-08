<?php
/**
* Ext4mage Orders2csv Module
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to Henrik Kier <info@ext4mage.com> so we can send you a copy immediately.
*
* @category   Ext4mage
* @package    Ext4mage_Orders2csv
* @copyright  Copyright (c) 2012 Ext4mage (http://ext4mage.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @author     Henrik Kier <info@ext4mage.com>
* */
class Ext4mage_Orders2csv_Block_Adminhtml_File_Edit_Tab_Generel extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('generel_fieldset', array('legend'=>Mage::helper('orders2csv')->__('Element information')));
		 
		$fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('orders2csv')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
		));

		$fieldset->addField('is_active', 'select', array(
          'label'     => Mage::helper('orders2csv')->__('Status'),
          'name'      => 'is_active',
          'required'  => true,
          'values'    => array(
		array(
                  'value'     => 1,
                  'label'     => Mage::helper('orders2csv')->__('Enabled'),
		),

		array(
                  'value'     => 2,
                  'label'     => Mage::helper('orders2csv')->__('Disabled'),
		),
		),
		));

		$fieldset->addField('num_formatting', 'select', array(
		          'label'     => Mage::helper('orders2csv')->__('Price formatting'),
		          'name'      => 'num_formatting',
		          'required'  => true,
		          'values'    => array(
		array(
		                  'value'     => 1,
		                  'label'     => Mage::helper('orders2csv')->__('None - (ex. 12231245)'),
		),
		array(
		                  'value'     => 2,
		                  'label'     => Mage::helper('orders2csv')->__('Decimal - (ex. 1223.1245)'),
		),
		array(
		                  'value'     => 3,
		                  'label'     => Mage::helper('orders2csv')->__('Text - (ex. $1,223.12)'),
		),
		),
		));
		
		$fieldset->addField('saveas', 'hidden', array(
	        'name'      => 'saveas',
	        'value'     => '0',
		));

		if ( Mage::getSingleton('adminhtml/session')->getOrders2csvData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getOrders2csvData());
			Mage::getSingleton('adminhtml/session')->setFileData(null);
		} elseif ( Mage::registry('orders2csv_data') ) {
			$form->setValues(Mage::registry('orders2csv_data')->getData());
		}
		return parent::_prepareForm();
	}
}