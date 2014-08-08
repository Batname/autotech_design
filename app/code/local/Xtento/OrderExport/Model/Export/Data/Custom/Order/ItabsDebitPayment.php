<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-11-19T18:56:28+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/ItabsDebitPayment.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_ItabsDebitPayment extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Itabs Debit Payment Data',
            'category' => 'Order Payment',
            'description' => 'Export bank account and bank number of Itabs_Debit extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('debitpayment')) {
            return $returnArray;
        }
        $payment = $collectionItem->getOrder()->getPayment();
        if ($payment->getMethod() == 'debit') {
            $this->_writeArray = & $returnArray['payment']['debitpayment'];

            // Fetch fields to export
            $this->writeValue('account_owner', $payment->getCcOwner());
            $this->writeValue('account_number', preg_replace("/[^0-9\-]/", "", Mage::helper('core')->decrypt($payment->getCcNumberEnc())));
            $this->writeValue('account_bankcode', preg_replace("/[^0-9\-]/", "", Mage::helper('core')->decrypt($payment->getCcType())));
            $this->writeValue('account_swift', Mage::helper('core')->decrypt($payment->getDebitSwift()));
            $this->writeValue('account_iban', Mage::helper('core')->decrypt($payment->getDebitIban()));
            if (Mage::helper('xtcore/utils')->isExtensionInstalled('Itabs_Debit') || Mage::helper('xtcore/utils')->isExtensionInstalled('Mage_Debit')) {
                $this->writeValue('account_bank', Mage::helper('debit/data')->getBankByBlz(preg_replace("/[^0-9]/", "", Mage::helper('core')->decrypt($payment->getCcType()))));
            }
        }
        return $returnArray;
    }
}