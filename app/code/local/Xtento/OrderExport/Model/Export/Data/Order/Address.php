<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-04-10T20:45:54+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Order/Address.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Order_Address extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        // Init cache
        /*if (!isset($this->_cache['region_code'])) {
            $this->_cache['region_code'] = array();
        }*/
        // Return config
        return array(
            'name' => 'Billing/Shipping Address',
            'category' => 'Order',
            'description' => 'Export the billing/shipping address.',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO, Xtento_OrderExport_Model_Export::ENTITY_QUOTE),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        // Fetch fields to export
        $order = $collectionItem->getOrder();

        // Billing Address
        if ($this->fieldLoadingRequired('billing')) {
            $this->_writeArray = & $returnArray['billing']; // Write on billing level
            $billingAddress = $order->getBillingAddress();
            if ($billingAddress && $billingAddress->getId()) {
                $billingAddress->explodeStreetAddress();
                foreach ($billingAddress->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
                // Region Code
                if ($billingAddress->getRegionId() !== NULL && $this->fieldLoadingRequired('region_code')) {
                    $this->writeValue('region_code', $billingAddress->getRegionModel()->getCode());
                }
                // Country - ISO3, Full Name
                if ($billingAddress->getCountryId() !== NULL) {
                    if ($this->fieldLoadingRequired('country_name')) $this->writeValue('country_name', Zend_Locale::getTranslation($billingAddress->getCountryId(), 'country', 'en_US'));
                    if ($this->fieldLoadingRequired('country_iso3')) $this->writeValue('country_iso3', $billingAddress->getCountryModel()->getIso3Code());
                }
                $this->_addEECustomAddressAttributes($billingAddress);
            }
        }

        // Shipping Address
        if ($this->fieldLoadingRequired('shipping')) {
            $this->_writeArray = & $returnArray['shipping']; // Write on billing level
            $shippingAddress = $order->getShippingAddress();
            if ($shippingAddress && $shippingAddress->getId()) {
                $shippingAddress->explodeStreetAddress();
                foreach ($shippingAddress->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
                // Region Code
                if ($shippingAddress->getRegionId() !== NULL && $this->fieldLoadingRequired('region_code')) {
                    $this->writeValue('region_code', $shippingAddress->getRegionModel()->getCode());
                }
                // Country - ISO3, Full Name
                if ($shippingAddress->getCountryId() !== NULL) {
                    if ($this->fieldLoadingRequired('country_name')) $this->writeValue('country_name', Zend_Locale::getTranslation($shippingAddress->getCountryId(), 'country', 'en_US'));
                    if ($this->fieldLoadingRequired('country_iso3')) $this->writeValue('country_iso3', $shippingAddress->getCountryModel()->getIso3Code());
                }
                $this->_addEECustomAddressAttributes($shippingAddress);
                // Split street into street, housenumber, add.. needs to be fixed/reworked.
                /*$streetSplit = explode(" ", preg_replace("/[[:blank:]]+/u", " ", $shippingAddress->getStreet1()));
                if (count($streetSplit) > 0) {
                    $streetName = str_replace($streetSplit[count($streetSplit) - 1], '', $shippingAddress->getStreet1());
                    $streetLast = preg_replace('/[^A-Za-z0-9]/', '', $streetSplit[count($streetSplit) - 1]);
                    if (is_numeric($streetLast)) {
                        $streetAdd = '';
                        $streetNumber = $streetLast;
                    } else {
                        $streetAdd = $streetLast[count($streetLast)];
                        $streetNumber = intval($streetLast);
                    }
                    $this->writeValue('street_first', trim($streetName));
                    $this->writeValue('street_number', $streetNumber);
                    $this->writeValue('street_add', $streetAdd);
                }*/
            }
        }

        // Done
        return $returnArray;
    }

    private function _addEECustomAddressAttributes($address)
    {
        // For Enterprise Edition, load custom "customer address attributes" and their potential values for "dropdown" attributes
        if (Mage::helper('xtcore/utils')->getIsPEorEE()) {
            $customerAddressEntity = Mage::getModel('eav/config')->getEntityType('customer_address');
            $customerAddressAttributes = Mage::getModel('eav/entity_attribute')->getCollection()->addFieldToFilter('entity_type_id', $customerAddressEntity->getId())->addFieldToFilter('source_model', 'eav/entity_attribute_source_table');
            foreach ($customerAddressAttributes as $addressAttribute) {
                $attributeCode = $addressAttribute->getAttributeCode();
                if ($address->getData($attributeCode)) {
                    try {
                        $attributeOptions = $addressAttribute->getSource()->getAllOptions();
                        foreach ($attributeOptions as $attributeOption) {
                            if ($attributeOption['value'] == $address->getData($attributeCode)) {
                                $this->writeValue($attributeCode . '_label', $attributeOption['label']);
                                break 1;
                            }
                        }
                    } catch (Exception $e) {
                    }
                }
            }
        }
    }
}