<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-09-08T16:10:39+02:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Profile/Edit/Tab/Fields.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Profile_Edit_Tab_Fields extends Xtento_OrderExport_Block_Adminhtml_Widget_Tab
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_orderexport')->__('Out of the box, all entities and fields related to the current export type can be exported. If you want to speed up the export, disable export entities/fields not required for this profile.'));
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('order_export_profile');
        // Set default values
        if (!$model->getId()) {
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('field_settings', array(
            'legend' => Mage::helper('xtento_orderexport')->__('Export Fields'),
            'class' => 'fieldset-wide',
        ));

        $fieldset->addField('field_note', 'note', array(
            'text' => Mage::helper('xtento_orderexport')->__('<strong>Important</strong>: Please save the profile after modifying the fields available for export, as otherwise you won\'t see any effect when testing the output format or retrieving the fields available for export.')
        ));

        $exportFieldsArray = array();
        $exportData = Mage::getModel('xtento_orderexport/export_data')->getExportData(Mage::registry('order_export_profile')->getEntity(), false, true);
        foreach ($exportData as $exportField) {
            $label = $exportField['configuration']['name'];
            if (!empty($exportField['configuration']['description']) || (isset($exportField['configuration']['third_party']) && $exportField['configuration']['third_party'] === TRUE)) {
                $label .= '<br><small>(';
                if (!empty($exportField['configuration']['description'])) {
                    $label .= 'Description: ' . $exportField['configuration']['description'];
                }
                if (isset($exportField['configuration']['third_party']) && $exportField['configuration']['third_party'] === TRUE) {
                    $label .= ' | Third-party module: <strong>Yes</strong>';
                }
                $label .= ')</small>';
            }
            $exportFieldsArray[$exportField['configuration']['category']][] = array('value' => $exportField['class_identifier'], 'label' => Mage::helper('xtento_orderexport')->__($label));
        }

        foreach ($exportFieldsArray as $categoryName => $exportFields) {
            $this->_array_sort_by_column($exportFields, 'label');
            $fieldset->addField(strtolower($categoryName), 'checkboxes', array(
                'label' => Mage::helper('xtento_orderexport')->__($categoryName),
                'name' => 'export_fields[]',
                'values' => $exportFields,
                'checked' => $this->_getCheckedExportFields($exportFields),
                'value' => $this->_getCheckedExportFields($exportFields),
                'note' => $this->_getCategoryNote($categoryName)
            ));
        }

        #$form->setValues($model->getData());

        return parent::_prepareForm();
    }

    private function _getCheckedExportFields($exportFields)
    {
        $modelValues = Mage::registry('order_export_profile')->getExportFields();
        if (empty($modelValues)) {
            // No value set, all checked
            $checkedFields = array();
            if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '<')) {
                // Fixing a bug in array_search for checkboxes data type
                array_push($checkedFields, 'fakefirstvalue');
            }
            foreach ($exportFields as $exportField) {
                array_push($checkedFields, $exportField['value']);
            }
            return $checkedFields;
        } else {
            if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                return explode(",", $modelValues);
            } else {
                // Fixing a bug in array_search for checkboxes data type
                $fixedArray = explode(",", $modelValues);
                array_unshift($fixedArray, '---');
                return $fixedArray;
            }
        }
    }

    private function _getCategoryNote($categoryName)
    {
        if ($categoryName == 'Order') {
            return Mage::helper('xtento_orderexport')->__('These fields are related to the order entity and should be accessible for any export entity.');
        }
        if ($categoryName == 'Order Payment') {
            return Mage::helper('xtento_orderexport')->__('These fields are related to the order_payment tables and should be accessible for any export entity.');
        }
        if ($categoryName == 'Shared') {
            return Mage::helper('xtento_orderexport')->__('These fields are related to all export entities and should be accessible for any export entity.');
        }
        if ($categoryName == 'Shipment') {
            return Mage::helper('xtento_orderexport')->__('These fields are related to the shipment entity and are only available when exporting shipments.');
        }
        if ($categoryName == 'Customer') {
            return Mage::helper('xtento_orderexport')->__('These fields are related to the customer entity and are available for all export entities.');
        }
        return '';
    }

    private function _array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
    {
        $sort_col = array();
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }
        array_multisort($sort_col, $dir, $arr);
    }
}