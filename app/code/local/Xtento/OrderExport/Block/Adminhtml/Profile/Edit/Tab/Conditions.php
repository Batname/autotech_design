<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-03-27T21:50:51+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Profile/Edit/Tab/Conditions.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Profile_Edit_Tab_Conditions extends Xtento_OrderExport_Block_Adminhtml_Widget_Tab
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_orderexport')->__('The settings specified below will be applied to all manual and automatic exports. For manual exports, this can be changed in the "Manual Export" screen before exporting. If an %s does not match the filters, it simply won\'t be exported.', Mage::registry('order_export_profile')->getEntity()));
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('order_export_profile');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('object_filters', array('legend' => Mage::helper('xtento_orderexport')->__('%s Filters', ucwords($model->getEntity())), 'class' => 'fieldset-wide'));

        $fieldset->addField('export_filter_new_only', 'select', array(
            'label' => Mage::helper('xtento_orderexport')->__('Export only new %ss', Mage::registry('order_export_profile')->getEntity()),
            'name' => 'export_filter_new_only',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_orderexport')->__('Regardless whether you\'re using manual, cronjob or the event-based export, if set to yes, this setting will make sure every %s gets exported only ONCE by this profile. This means, even if another export event gets called, if the %s has been already exported by this profile, it won\'t be exported again. You can "reset" exported objects in the "Profile Export History" tab.<br/>Example usage: Set up a cronjob export which exports all "Processing" orders and set this to "Yes" - every "Processing" order will be exported only ONCE.', Mage::registry('order_export_profile')->getEntity(), Mage::registry('order_export_profile')->getEntity())
        ));

        $fieldset->addField('store_ids', 'multiselect', array(
            'label' => Mage::helper('xtento_orderexport')->__('Store Views'),
            'name' => 'store_ids[]',
            'values' => array_merge_recursive(array(array('value' => '', 'label' => Mage::helper('xtento_orderexport')->__('--- All Store Views ---'))), Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm()),
            'note' => Mage::helper('xtento_orderexport')->__('Leave empty or select all to export any store. Hold CTRL on your keyboard to pick specific stores.'),
        ));

        $fieldset->addField('export_filter_datefrom', 'date', array(
            'label' => Mage::helper('xtento_orderexport')->__('Date From'),
            'name' => 'export_filter_datefrom',
            'format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'note' => Mage::helper('xtento_orderexport')->__('Export only %ss created after date X (including day X).', Mage::registry('order_export_profile')->getEntity())
        ));

        $fieldset->addField('export_filter_dateto', 'date', array(
            'label' => Mage::helper('xtento_orderexport')->__('Date To'),
            'name' => 'export_filter_dateto',
            'format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'note' => Mage::helper('xtento_orderexport')->__('Export only %ss created before date X (including day X).', Mage::registry('order_export_profile')->getEntity())
        ));

        $fieldset->addField('export_filter_last_x_days', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Created during the last X days'),
            'name' => 'export_filter_last_x_days',
            'maxlength' => 5,
            'style' => 'width: 50px !important;" min="0',
            'note' => Mage::helper('xtento_orderexport')->__('Export only %ss created during the last X days (including day X). Only enter numbers here, nothing else. Leave empty if no "created during the last X days" filter should be applied.', Mage::registry('order_export_profile')->getEntity())
        ))->setType('number');

        $fieldset->addField('export_filter_older_x_minutes', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Older than X minutes'),
            'name' => 'export_filter_older_x_minutes',
            'maxlength' => 10,
            'style' => 'width: 75px !important;" min="1',
            'note' => Mage::helper('xtento_orderexport')->__('Export only %ss which have been created at least X minutes ago. Only enter numbers here, nothing else. Leave empty if no filter should be applied.', Mage::registry('order_export_profile')->getEntity())
        ))->setType('number');

        if (Mage::registry('order_export_profile')->getEntity() !== Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT && Mage::registry('order_export_profile')->getEntity() !== Xtento_OrderExport_Model_Export::ENTITY_QUOTE && Mage::registry('order_export_profile')->getEntity() !== Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
            // Not available for shipments
            $fieldset->addField('export_filter_status', 'multiselect', array(
                'label' => Mage::helper('xtento_orderexport')->__('%s Status', ucfirst(Mage::registry('order_export_profile')->getEntity())),
                'name' => 'export_filter_status',
                'values' => array_merge_recursive(array(array('value' => '', 'label' => Mage::helper('xtento_orderexport')->__('--- All statuses ---'))), Mage::getSingleton('xtento_orderexport/system_config_source_export_status')->toOptionArray(Mage::registry('order_export_profile')->getEntity())),
                'note' => Mage::helper('xtento_orderexport')->__('Export only %ss with status X. Hold down CTRL to select multiple.', Mage::registry('order_export_profile')->getEntity())
            ));
        }

        if (Mage::registry('order_export_profile')->getEntity() !== Xtento_OrderExport_Model_Export::ENTITY_QUOTE && Mage::registry('order_export_profile')->getEntity() !== Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
            $fieldset = $form->addFieldset('item_filters', array('legend' => Mage::helper('xtento_orderexport')->__('Item Filters'), 'class' => 'fieldset-wide'));

            $fieldset->addField('export_filter_product_type', 'multiselect', array(
                'label' => Mage::helper('xtento_orderexport')->__('Hidden Product Types'),
                'name' => 'export_filter_product_type',
                'values' => array_merge_recursive(array(array('value' => '', 'label' => Mage::helper('xtento_orderexport')->__('--- No hidden product types ---'))), Mage::getModel('catalog/product_type')->getOptions()),
                'note' => Mage::helper('xtento_orderexport')->__('The selected product types won\'t be exported and won\'t show up in the output format for this profile. You can still fetch information from the parent product in the XSL Template using the <i>parent_item/</i> node. ')
            ));
        }

        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.1', '>')) {
            if (Mage::registry('order_export_profile')->getEntity() !== Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                    ->setTemplate('promo/fieldset.phtml')
                    ->setNewChildUrl($this->getUrl('*/orderexport_profile/newConditionHtml/form/rule_conditions_fieldset', array('profile_id' => Mage::registry('order_export_profile')->getId())));

                $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
                    'legend' => Mage::helper('xtento_orderexport')->__('Additional filters: Export %s only if the following conditions are met', Mage::registry('order_export_profile')->getEntity()),
                ))->setRenderer($renderer);

                $fieldset->addField('conditions', 'text', array(
                    'name' => 'conditions',
                    'label' => Mage::helper('salesrule')->__('Conditions'),
                    'title' => Mage::helper('salesrule')->__('Conditions'),
                ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
            }
        }

        if (Mage::registry('order_export_profile')->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_ORDER) {
            $fieldset = $form->addFieldset('actions', array('legend' => Mage::helper('xtento_orderexport')->__('Actions'), 'class' => 'fieldset-wide',));

            // Only available for orders
            $fieldset->addField('export_action_change_status', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Change %s status after export', Mage::registry('order_export_profile')->getEntity()),
                'name' => 'export_action_change_status',
                'values' => Mage::getSingleton('xtento_orderexport/system_config_source_order_status')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('Change %s status to X after exporting.', Mage::registry('order_export_profile')->getEntity())
            ));
            $fieldset->addField('export_action_invoice_order', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Invoice order after exporting'),
                'name' => 'export_action_invoice_order',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('If enabled, after exporting, the order would be invoiced and the payment would be captured.')
            ));
            $fieldset->addField('export_action_invoice_notify', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Notify customer about invoice'),
                'name' => 'export_action_invoice_notify',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('If "Invoice order after exporting" is enabled, the customer would receive an email after the invoice has been created.')
            ));
            $fieldset->addField('export_action_ship_order', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Ship order after exporting'),
                'name' => 'export_action_ship_order',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('If enabled, after exporting, the order would be shipped.')
            ));
            $fieldset->addField('export_action_ship_notify', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Notify customer about shipment'),
                'name' => 'export_action_ship_notify',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('If "Ship order after exporting" is enabled, the customer would receive an email after the shipment has been created.')
            ));
            $fieldset->addField('export_action_cancel_order', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Cancel order'),
                'name' => 'export_action_cancel_order',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('If set to "Yes", this will cancel the order after the profile has been executed.')
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
