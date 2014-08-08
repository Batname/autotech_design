<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-11-20T14:26:28+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/AmastyOrderAttributes.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_AmastyOrderAttributes extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Amasty Order Attributes Export',
            'category' => 'Order',
            'description' => 'Export custom order attributes of Amasty Order Attributes extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Amasty_Orderattr',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['amasty_orderattributes']; // Write on "amasty_orderattributes" level
        // Fetch fields to export
        $order = $collectionItem->getOrder();

        if (!$this->fieldLoadingRequired('amasty_orderattributes')) {
            return $returnArray;
        }

        try {
            $orderAttributes = Mage::getModel('amorderattr/attribute')->load($order->getId(), 'order_id');
            $attributeCollection = Mage::getModel('eav/entity_attribute')->getCollection();
            $attributeCollection->addFieldToFilter('entity_type_id', Mage::getModel('eav/entity')->setType('order')->getTypeId());
            $attributes = $attributeCollection->load();

            if ($attributes->getSize()) {
                foreach ($attributes as $attribute) {
                    if (!$this->fieldLoadingRequired($attribute->getAttributeCode())) {
                        continue;
                    }
                    $value = '';
                    switch ($attribute->getFrontendInput()) {
                        case 'select':
                        case 'boolean':
                            $values = $this->_getStoreValues($attribute);
                            $options = $attribute->getSource()->getAllOptions(true, true);
                            foreach ($options as $i => $option) {
                                if (isset($values[$option['value']]) && ($option['value'] == $orderAttributes[$attribute->getData('attribute_code')])) {
                                    $value = $values[$option['value']];
                                } elseif ($option['value'] == $orderAttributes->getData($attribute->getAttributeCode())) {
                                    $value = $option['label'];
                                }
                            }
                            break;
                        case 'checkboxes':
                            $values = $this->_getStoreValues($attribute);
                            $options = $attribute->getSource()->getAllOptions(true, true);
                            $checkboxValues = explode(',', $orderAttributes->getData($attribute->getAttributeCode()));
                            foreach ($options as $i => $option) {
                                if (in_array($option['value'], $checkboxValues)) {
                                    $value[] = $values[$option['value']];
                                }
                            }
                            $value = implode(', ', $value);
                            break;
                        default:
                            $value = $orderAttributes->getData($attribute->getAttributeCode());
                            break;
                    }
                    $this->writeValue($attribute->getAttributeCode(), $value);
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }


    private function _getStoreValues($attribute)
    {
        $values = array();
        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setStoreFilter(Mage::app()->getStore()->getId(), false)
            ->load();
        foreach ($valuesCollection as $item) {
            $values[$item->getId()] = $item->getValue();
        }
        // fix for `No default store view`
        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setStoreFilter(0, false)
            ->load();
        foreach ($valuesCollection as $item) {
            if (isset($values[$item->getId()]) && ($values[$item->getId()] !== '')) {
                continue;
            } else {
                $values[$item->getId()] = $item->getValue();
            }
        }
        return $values;
    }
}