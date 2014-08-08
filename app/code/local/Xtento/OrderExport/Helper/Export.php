<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-01-17T09:36:22+01:00
 * File:          app/code/local/Xtento/OrderExport/Helper/Export.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Helper_Export extends Mage_Core_Helper_Abstract
{
    public function getExportEntity($entity)
    {
        if ($entity == Xtento_OrderExport_Model_Export::ENTITY_ORDER) {
            return 'sales/order';
        } else if ($entity == Xtento_OrderExport_Model_Export::ENTITY_INVOICE) {
            return 'sales/order_invoice';
        } else if ($entity == Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT) {
            return 'sales/order_shipment';
        } else if ($entity == Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO) {
            return 'sales/order_creditmemo';
        } else if ($entity == Xtento_OrderExport_Model_Export::ENTITY_QUOTE) {
            return 'sales/quote';
        } else if ($entity == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
            return 'customer/customer';
        }
        Mage::throwException(Mage::helper('xtento_orderexport')->__('Could not find export entity "%s"', $entity));
    }

    public function getLastIncrementId($entity)
    {
        if ($entity == Xtento_OrderExport_Model_Export::ENTITY_QUOTE) {
            $collection = Mage::getModel($this->getExportEntity($entity))->getCollection()
                ->addFieldToSelect('entity_id');
            $collection->getSelect()->limit(1)->order('entity_id DESC');
        } else if ($entity == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
            $collection = Mage::getModel($this->getExportEntity($entity))->getCollection()
                ->addAttributeToSelect('entity_id');
            $collection->getSelect()->limit(1)->order('entity_id DESC');
        } else {
            $collection = Mage::getModel($this->getExportEntity($entity))->getCollection()
                ->addAttributeToSelect('increment_id')
                ->addAttributeToSort('entity_id', 'desc')
                ->setPage(1, 1);
        }
        $object = $collection->getFirstItem();
        return ($object->getIncrementId() ? $object->getIncrementId() : $object->getId());
    }

    public function getExportBkpDir()
    {
        return Mage::getBaseDir('var') . DS . "export_bkp" . DS;
    }
}