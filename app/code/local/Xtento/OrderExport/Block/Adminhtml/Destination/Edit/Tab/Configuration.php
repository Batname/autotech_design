<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-07-09T12:08:14+02:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Destination/Edit/Tab/Configuration.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Destination_Edit_Tab_Configuration extends Xtento_OrderExport_Block_Adminhtml_Widget_Tab
{
    protected function _prepareForm()
    {
        $model = Mage::registry('destination');
        // Set default values
        if (!$model->getId()) {
            $model->setEnabled(1);
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('xtento_orderexport')->__('Destination Settings'),
        ));

        if ($model->getId()) {
            $fieldset->addField('destination_id', 'hidden', array(
                'name' => 'destination_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Name'),
            'name' => 'name',
            'required' => true,
            'note' => Mage::helper('xtento_orderexport')->__('Assign a name to identify this destination in logs/profiles.')
        ));

        if ($model->getId()) {
            $typeNote = 'Changing the destination type will reload the page.';
        } else {
            $typeNote = '';
        }

        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('xtento_orderexport')->__('Destination Type'),
            'name' => 'type',
            'options' => array_merge(array('' => Mage::helper('xtento_orderexport')->__('--- Please Select ---')), Mage::getModel('xtento_orderexport/system_config_source_destination_type')->toOptionArray()),
            'required' => true,
            'onchange' => ($model->getId()) ? 'if (this.value==\'\') { return false; } editForm.submitUrl = $(\'edit_form\').action+\'continue/edit/switch/true\'; editForm._submit();' : '',
            'note' => Mage::helper('xtento_orderexport')->__($typeNote)
        ));

        if (!Mage::registry('destination') || !Mage::registry('destination')->getId()) {
            $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
            ));
        }

        if ($model->getId()) {
            $fieldset->addField('status', 'text', array(
                'label' => Mage::helper('xtento_orderexport')->__('Status'),
                'name' => 'status',
                'disabled' => true,
            ));
            $model->setStatus(Mage::helper('xtento_orderexport')->__('Used in %d profile(s)', count($model->getProfileUsage())));

            $fieldset->addField('last_result_message', 'textarea', array(
                'label' => Mage::helper('xtento_orderexport')->__('Last Result Message'),
                'name' => 'last_result_message_dis',
                'disabled' => true,
                'style' => 'height: 90px',
            ));

            $this->_addFieldsForType($form, $model->getType());
        }

        $form->setValues($model->getData());

        return parent::_prepareForm();
    }

    private function _addFieldsForType($form, $type)
    {
        return Mage::getBlockSingleton('xtento_orderexport/adminhtml_destination_edit_tab_type_' . $type)->getFields($form);
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