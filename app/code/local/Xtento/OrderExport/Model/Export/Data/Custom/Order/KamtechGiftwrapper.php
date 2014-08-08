<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-10-18T19:22:18+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/KamtechGiftwrapper.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_KamtechGiftwrapper extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Kamtech Giftwrapper message export',
            'category' => 'Order',
            'description' => 'Export gift wrap message of Kamtech Giftwrapper extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Kamtech_Giftwrapper',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('kamtech_giftwrapper')) {
            return $returnArray;
        }
        $order = $collectionItem->getOrder();
        $this->_writeArray = & $returnArray['kamtech_giftwrapper'];

        // Fetch fields to export
        $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $dataRow = $readAdapter->fetchRow("SELECT message from ".Mage::getSingleton('core/resource')->getTableName('sales_order_giftwrap_message')." WHERE order_id = " . $readAdapter->quote($order->getIncrementId()));

        if (is_array($dataRow)) {
            foreach ($dataRow as $key => $value) {
                $this->writeValue($key, $value);
            }
        }
        return $returnArray;
    }
}