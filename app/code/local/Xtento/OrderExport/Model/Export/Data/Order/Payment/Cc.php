<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-01-14T17:21:34+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Order/Payment/Cc.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Order_Payment_Cc extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Credit Card Information',
            'category' => 'Order Payment',
            'description' => 'Export decrypted credit card information for payment methods saving the CC# into the cc_number_enc field.',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO)
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['payment']; // Write into payment fields
        // Fetch fields to export
        $payment = $collectionItem->getOrder()->getPayment();
        if ($payment) {
            $this->writeValue('cc_number_dec', preg_replace("/[^0-9\-]/", "", Mage::helper('core')->decrypt($payment->getCcNumberEnc())));
            $this->writeValue('cc_cvv2', preg_replace("/[^0-9\-]/", "", $payment->getCcCid()));
            $this->writeValue('cc_cvv2_dec', preg_replace("/[^0-9\-]/", "", Mage::helper('core')->decrypt($payment->getCcCidEnc())));
        }
        // Done
        return $returnArray;
    }
}