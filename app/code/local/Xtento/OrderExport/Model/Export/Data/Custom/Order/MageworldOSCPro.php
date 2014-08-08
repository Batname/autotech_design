<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-10-08T18:51:19+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/MageworldOSCPro.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_MageworldOSCPro extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'MageWorld OneStepCheckout Pro Export',
            'category' => 'Order',
            'description' => 'Export custom order attributes of MageWorld OneStepCheckout Pro extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'MW_Onestepcheckout',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['mw_onestepcheckout']; // Write on "mw_onestepcheckout" level
        // Fetch fields to export
        $order = $collectionItem->getOrder();

        if (!$this->fieldLoadingRequired('mw_onestepcheckout')) {
            return $returnArray;
        }

        try {
            $dataCollection = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection();
            $dataCollection->addFieldToFilter('sales_order_id', $order->getId());

            // The collection should contain fields such as mw_customercomment_info, mw_deliverydate_date, mw_deliverydate_time

            if ($dataCollection->count()) {
                foreach ($dataCollection as $dataRow) {
                    foreach ($dataRow->getData() as $key => $value) {
                        $this->writeValue($key, $value);
                    }
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}