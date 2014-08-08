<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-05-17T16:22:31+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Customer/MageMeWebFormsCrf.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Customer_MageMeWebFormsCrf extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'MageMe Web Forms Customer Registration Fields',
            'category' => 'Customer',
            'description' => 'Export customer registration fields',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO, Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER),
            'third_party' => true,
            'depends_module' => 'VladimirPopov_WebFormsCRF',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['webformscrf'];
        if (!$this->fieldLoadingRequired('webformscrf')) {
            return $returnArray;
        }
        // Fetch fields to export
        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
            $customer = Mage::getModel('customer/customer')->load($collectionItem->getObject()->getId());
        } else {
            $order = $collectionItem->getOrder();
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            if (!$customer || !$customer->getId()) {
                return $returnArray;
            }
        }


        try {
            $webformId = Mage::getStoreConfig('webformscrf/registration/form', $customer->getStoreId());
            $group = Mage::getModel('customer/group')->load($customer->getGroupId());
            if ($group->getWebformId()) {
                $webformId = $group->getWebformId();
            }
            $collection = Mage::getModel('webforms/results')->getCollection()
                ->addFilter('webform_id', $webformId)
                ->addFilter('customer_id', $customer->getEntityId());

            $collection->getSelect()->order('created_time desc')->limit('1');
            $collection->load();
            if ($collection->count() > 0) {
                $result = $collection->getFirstItem();
                foreach ($result->getField() as $field_id => $value) {
                    $field = Mage::getModel('webforms/fields')->load($field_id);
                    switch ($field->getType()) {
                        case 'file':
                        case 'image':
                            $value = Varien_File_Uploader::getCorrectFileName($value);
                            $this->writeValue('field_' . $field_id . '_url', $result->getDownloadLink($field_id, $value));
                            break;
                    }
                    $this->writeValue('field_' . $field_id, $value);
                    if ($field->getCode()) {
                        $this->writeValue($field->getCode(), $value);
                    }
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}