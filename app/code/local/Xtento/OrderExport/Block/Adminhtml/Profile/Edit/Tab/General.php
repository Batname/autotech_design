<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-09-08T16:10:39+02:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Profile/Edit/Tab/General.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Profile_Edit_Tab_General extends Xtento_OrderExport_Block_Adminhtml_Widget_Tab
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $model = Mage::registry('order_export_profile');
        if ($model->getId() && !$model->getEnabled()) {
            $formMessages[] = array('type' => 'warning', 'message' => Mage::helper('xtento_orderexport')->__('This profile is disabled. No automatic exports will be made and the profile won\'t show up for manual exports.'));
        }
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('order_export_profile');
        // Set default values
        if (!$model->getId()) {
            $model->setEnabled(1);
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('xtento_orderexport')->__('General Configuration'),
        ));

        if ($model->getId()) {
            $fieldset->addField('profile_id', 'hidden', array(
                'name' => 'profile_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Name'),
            'name' => 'name',
            'required' => true,
        ));

        if ($model->getId()) {
            $fieldset->addField('enabled', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Enabled'),
                'name' => 'enabled',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
            ));
        }

        $entity = $fieldset->addField('entity', 'select', array(
            'label' => Mage::helper('xtento_orderexport')->__('Export Type'),
            'name' => 'entity',
            'options' => Mage::getSingleton('xtento_orderexport/system_config_source_export_entity')->toOptionArray(),
            'required' => true,
            'note' => Mage::helper('xtento_orderexport')->__('This setting can\'t be changed after creating the profile. Add a new profile for different export types.')
        ));
        if ($model->getId() && !Mage::getSingleton('adminhtml/session')->getProfileDuplicated()) {
            // 1.3 Compatibility. Does not accept the disabled param directly in the addField array.
            $entity->setDisabled(true);
        }

        if (!Mage::registry('order_export_profile') || !Mage::registry('order_export_profile')->getId()) {
            $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
            ));
        }

        if (Mage::registry('order_export_profile') && Mage::registry('order_export_profile')->getId()) {
            $fieldset = $form->addFieldset('advanced_fieldset', array(
                'legend' => Mage::helper('xtento_orderexport')->__('Export Settings'),
                'class' => 'fieldset-wide',
            ));

            $fieldset->addField('save_files_local_copy', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Save local copies of exports'),
                'name' => 'save_files_local_copy',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('If set to yes, local copies of the exported files will be saved in the ./var/export_bkp/ folder. If set to no, you won\'t be able to download old export files from the export/execution log.')
            ));

            $fieldset->addField('export_one_file_per_object', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Export each %s separately', Mage::registry('order_export_profile')->getEntity()),
                'name' => 'export_one_file_per_object',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('If set to yes, each %s exported would be saved in a separate file. This means, for every %s you export, one file will be created, with just the one %s in there. If set to no, one file will be created with all the exported %ss in there.', Mage::registry('order_export_profile')->getEntity(), Mage::registry('order_export_profile')->getEntity(), Mage::registry('order_export_profile')->getEntity(), Mage::registry('order_export_profile')->getEntity())
            ));
        }

        $form->setValues($model->getData());

        return parent::_prepareForm();
    }

    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('catalog')->__('Continue'),
                'onclick' => "saveAndContinueEdit()",
                'class' => 'save'
            ))
        );
        return parent::_prepareLayout();
    }
}