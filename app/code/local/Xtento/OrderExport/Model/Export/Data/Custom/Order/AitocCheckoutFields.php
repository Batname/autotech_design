<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-05-06T19:48:09+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/AitocCheckoutFields.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_AitocCheckoutFields extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Aitoc Checkout Fields Export',
            'category' => 'Order',
            'description' => 'Export custom order/customer attributes of Aitoc Checkout Fields Manager extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Aitoc_Aitcheckoutfields',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        // Fetch fields to export
        $order = $collectionItem->getOrder();

        if (!$this->fieldLoadingRequired('aitoc_aitcheckoutfields') && !$this->fieldLoadingRequired('aitoc_aitcustomerfields')) {
            return $returnArray;
        }

        try {
            $oAitcheckoutfields = Mage::getModel('aitcheckoutfields/aitcheckoutfields');
            if ($this->fieldLoadingRequired('aitoc_aitcheckoutfields')) {
                $this->_writeArray = & $returnArray['aitoc_aitcheckoutfields']; // Write on "aitoc_aitcheckoutfields" level
                $customAttrList = $oAitcheckoutfields->getOrderCustomData($order->getEntityId(), $order->getStoreId(), true);
                foreach ($customAttrList as $aCustomAttrList) {
                    if (isset($aCustomAttrList['code']) && isset($aCustomAttrList['value'])) {
                        if (!empty($aCustomAttrList['code'])) $this->writeValue($aCustomAttrList['code'], $aCustomAttrList['value']);
                    }
                }
            }
            if ($this->fieldLoadingRequired('aitoc_aitcustomerfields') && $order->getCustomerId()) {
                $this->_writeArray = & $returnArray['aitoc_aitcustomerfields']; // Write on "aitoc_aitcustomerfields" level
                $customAttrList = $oAitcheckoutfields->getCustomerData($order->getCustomerId(), $order->getStoreId(), true);
                foreach ($customAttrList as $aCustomAttrList) {
                    if (isset($aCustomAttrList['code']) && isset($aCustomAttrList['value'])) {
                        if (!empty($aCustomAttrList['code'])) $this->writeValue($aCustomAttrList['code'], $aCustomAttrList['value']);
                    }
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}