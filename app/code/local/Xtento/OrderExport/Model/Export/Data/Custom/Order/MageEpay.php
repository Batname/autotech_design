<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-08-30T12:26:14+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/MageEpay.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_MageEpay extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'ePay Payment Gateway Export',
            'category' => 'Payment',
            'description' => 'Export payment information of the ePay payment gateway',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Mage_Epay',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('epay')) {
            return $returnArray;
        }
        $payment = $collectionItem->getOrder()->getPayment();
        if ($payment->getMethod() == 'epay_standard') {
            $this->_writeArray = & $returnArray['payment']['epay'];

            // Fetch fields to export
            $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $dataRow = $readAdapter->fetchRow("SELECT * from epay_order_status WHERE orderid = " . $readAdapter->quote($payment->getOrder()->getIncrementId())); // Yes, the table is always called epay_order_status - no database prefix intended by their developers

            if (is_array($dataRow)) {
                foreach ($dataRow as $key => $value) {
                    $this->writeValue($key, $value);
                }
            }
        }
        return $returnArray;
    }
}