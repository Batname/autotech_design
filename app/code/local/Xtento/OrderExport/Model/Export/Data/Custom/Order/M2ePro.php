<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-01-29T15:57:33+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/M2ePro.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_M2ePro extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'M2EPro m2epro_*_order data export',
            'category' => 'Order',
            'description' => 'Export additional order information stored by the M2EPro extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Ess_M2ePro',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('m2epro')) {
            return $returnArray;
        }

        $order = $collectionItem->getOrder();
        $payment = $order->getPayment();
        if ($payment->getMethod() == 'm2epropayment') {
            $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $additionalData = $payment->getAdditionalData();
            $additionalData = @unserialize($additionalData);
            if ($additionalData && is_array($additionalData)) {
                if (isset($additionalData['component_mode'])) {
                    $componentMode = $additionalData['component_mode'];

                    // Fetch fields to export
                    if ($componentMode == 'ebay') {
                        $this->_writeArray = & $returnArray['m2epro_ebay'];
                        $dataCollection = Mage::getModel('M2ePro/Ebay_Order')->getCollection();
                    } else if ($componentMode == 'amazon') {
                        $this->_writeArray = & $returnArray['m2epro_amazon'];
                        $dataCollection = Mage::getModel('M2ePro/Amazon_Order')->getCollection();
                    } else if ($componentMode == 'buy') {
                        $this->_writeArray = & $returnArray['m2epro_buy'];
                        $dataCollection = Mage::getModel('M2ePro/Buy_Order')->getCollection();
                    } else if ($componentMode == 'play') {
                        $this->_writeArray = & $returnArray['m2epro_play'];
                        $dataCollection = Mage::getModel('M2ePro/Play_Order')->getCollection();
                    }
                    if (isset($dataCollection)) {
                        $orderId = $readAdapter->fetchRow("SELECT id from " . Mage::getSingleton('core/resource')->getTableName('m2epro_order') . " WHERE magento_order_id = " . $readAdapter->quote($order->getId()));
                        if (is_array($orderId) && array_key_exists("id", $orderId)) {
                            $dataCollection->addFieldToFilter('order_id', $orderId['id']);

                            if ($dataCollection->count()) {
                                $dataRow = $dataCollection->getFirstItem();
                                foreach ($dataRow->getData() as $key => $value) {
                                    $this->writeValue($key, $value);
                                }
                            }
                        }
                    }

                    // Fetch custom field
                    /*
                    if ($componentMode == 'ebay') {
                        $this->_writeArray = & $returnArray['m2epro_ebay_account'];
                        $dataRow = $readAdapter->fetchRow("SELECT account_id from " . Mage::getSingleton('core/resource')->getTableName('m2epro_order') . " WHERE magento_order_id = " . $readAdapter->quote($order->getId()));
                        if (is_array($dataRow) && array_key_exists("account_id", $dataRow)) {
                            $dataRow = $readAdapter->fetchRow("SELECT ebay_info from " . Mage::getSingleton('core/resource')->getTableName('m2epro_ebay_account') . " WHERE account_id = " . $readAdapter->quote($dataRow['account_id']));
                            if (is_array($dataRow) && array_key_exists("ebay_info", $dataRow)) {
                                $dataRow = @json_decode($dataRow['ebay_info']);
                                foreach ($dataRow as $key => $value) {
                                    if (is_array($value) || is_object($value)) continue;
                                    $this->writeValue($key, $value);
                                }
                            }
                        }
                    }
                    */
                }
            }
        }
        return $returnArray;
    }
}