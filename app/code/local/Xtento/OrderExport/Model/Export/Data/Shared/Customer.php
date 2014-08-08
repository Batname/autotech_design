<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-04-02T14:21:45+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Shared/Customer.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Shared_Customer extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        // Init cache
        if (!isset($this->_cache['customer_group'])) {
            $this->_cache['customer_group'] = array();
        }
        // Return config
        return array(
            'name' => 'Customer information',
            'category' => 'Customer',
            'description' => 'Export customer information from customer tables.',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO, Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        // Fetch fields to export
        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
            $customer = Mage::getModel('customer/customer')->load($collectionItem->getObject()->getId());
            $this->_writeArray = & $returnArray; // Write on main level
            // Is subscribed to newsletter
            if ($this->fieldLoadingRequired('is_subscribed')) {
                $subscription = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
                if ($subscription->getId()) {
                    $this->writeValue('is_subscribed', $subscription->isSubscribed());
                } else {
                    $this->writeValue('is_subscribed', '0');
                }
            }
        } else {
            $this->_writeArray = & $returnArray['customer']; // Write on customer level
            $order = $collectionItem->getOrder();
            // Is subscribed to newsletter
            if ($this->fieldLoadingRequired('is_subscribed')) {
                $subscription = Mage::getModel('newsletter/subscriber')->loadByEmail($order->getCustomerEmail());
                if ($subscription->getId()) {
                    $this->writeValue('is_subscribed', $subscription->isSubscribed());
                } else {
                    $this->writeValue('is_subscribed', '0');
                }
            }
            // Load customer
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            if (!$customer || !$customer->getId()) {
                if ($this->getShowEmptyFields()) { // If this is debug mode and no customer was found, still output the customer attribute codes
                    $collection = Mage::getResourceModel('customer/customer_collection')
                        ->addAttributeToSelect('*');
                    $collection->getSelect()->limit(1, 0); // At least one customer must exist for this to work
                    if ($customer = $collection->getFirstItem()) {
                        foreach ($customer->getData() as $key => $value) {
                            if ($key == 'entity_id') {
                                continue;
                            }
                            $this->writeValue($key, NULL);
                        }
                    }
                }
                return $returnArray;
            }
        }

        if ($entityType !== Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER && !$this->fieldLoadingRequired('customer')) {
            return $returnArray;
        }

        // Customer data
        foreach ($customer->getData() as $key => $value) {
            if ($key == 'entity_id') {
                continue;
            }
            $this->writeValue($key, $value);
        }

        // Customer group
        if ($this->fieldLoadingRequired('customer_group')) {
            if (isset($this->_cache['customer_group'][$customer->getGroupId()])) {
                $this->writeValue('customer_group', $this->_cache['customer_group'][$customer->getGroupId()]);
            } else {
                $customerGroup = Mage::getModel('customer/group')->load($customer->getGroupId());
                if ($customerGroup && $customerGroup->getId()) {
                    $this->writeValue('customer_group', $customerGroup->getCustomerGroupCode());
                    $this->_cache['customer_group'][$customer->getGroupId()] = $customerGroup->getCustomerGroupCode();
                }
            }
        }

        // Has this customer purchased yet + order count
        if ($this->fieldLoadingRequired('has_purchased') || $this->fieldLoadingRequired('order_count')) {
            $customerOrders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId());

            $orderCount = $customerOrders->getSize();
            if ($orderCount > 0) {
                $this->writeValue('has_purchased', '1');
                $this->writeValue('order_count', $orderCount);
            } else {
                $this->writeValue('has_purchased', '0');
                $this->writeValue('order_count', '0');
            }
        }

        // First order date + last order date
        if ($this->fieldLoadingRequired('first_order_timestamp')) {
            $customerOrders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId())
                ->setOrder('created_at', 'ASC');
            if ($customerOrder = $customerOrders->getFirstItem()) {
                $this->writeValue('first_order_timestamp', Mage::helper('xtento_orderexport/date')->convertDateToStoreTimestamp($customerOrder->getCreatedAt()));
            } else {
                $this->writeValue('first_order_timestamp', 0);
            }
        }
        if ($this->fieldLoadingRequired('last_order_timestamp')) {
            $customerOrders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId())
                ->setOrder('created_at', 'DESC');
            if ($customerOrder = $customerOrders->getFirstItem()) {
                $this->writeValue('last_order_timestamp', Mage::helper('xtento_orderexport/date')->convertDateToStoreTimestamp($customerOrder->getCreatedAt()));
            } else {
                $this->writeValue('last_order_timestamp', 0);
            }
        }

        $this->_addEECustomAttributes($customer);

        // Timestamps of creation/update
        /*if ($this->fieldLoadingRequired('created_at_timestamp')) $this->writeValue('created_at_timestamp', Mage::helper('xtento_orderexport/date')->convertDateToStoreTimestamp($customer->getCreatedAt()));
        if ($this->fieldLoadingRequired('updated_at_timestamp')) $this->writeValue('updated_at_timestamp', Mage::helper('xtento_orderexport/date')->convertDateToStoreTimestamp($customer->getUpdatedAt()));*/

        // Customer addresses
        $addressCollection = $customer->getAddressesCollection();
        if (!empty($addressCollection) && $this->fieldLoadingRequired('addresses')) {
            foreach ($addressCollection as $customerAddress) {
                if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                    $this->_writeArray = & $returnArray['addresses'][];
                } else {
                    $this->_writeArray = & $returnArray['customer']['addresses'][];
                }
                $customerAddress->explodeStreetAddress();
                foreach ($customerAddress->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
                 // Region Code
                if ($customerAddress->getRegionId() !== NULL && $this->fieldLoadingRequired('region_code')) {
                    $this->writeValue('region_code', $customerAddress->getRegionModel()->getCode());
                }
                // Country - ISO3, Full Name
                if ($customerAddress->getCountryId() !== NULL) {
                    if ($this->fieldLoadingRequired('country_name')) $this->writeValue('country_name', Zend_Locale::getTranslation($customerAddress->getCountryId(), 'country', 'en_US'));
                    if ($this->fieldLoadingRequired('country_iso3')) $this->writeValue('country_iso3', $customerAddress->getCountryModel()->getIso3Code());
                }
                if ($customerAddress->getId() === $customer->getDefaultBilling() && $customerAddress->getId() === $customer->getDefaultShipping()) {
                    $this->writeValue('address_type', 'default_billing_shipping');
                } else if ($customerAddress->getId() === $customer->getDefaultBilling()) {
                    $this->writeValue('address_type', 'default_billing');
                } else if ($customerAddress->getId() === $customer->getDefaultShipping()) {
                    $this->writeValue('address_type', 'default_shipping');
                } else {
                    $this->writeValue('address_type', 'address');
                }
            }
        }

        // Done
        return $returnArray;
    }

    private function _addEECustomAttributes($customer)
    {
        // For Enterprise Edition, load custom "customer address attributes" and their potential values for "dropdown" attributes
        if (Mage::helper('xtcore/utils')->getIsPEorEE()) {
            $customerEntity = Mage::getModel('eav/config')->getEntityType('customer');
            $customerAttributes = Mage::getModel('eav/entity_attribute')->getCollection()->addFieldToFilter('entity_type_id', $customerEntity->getId())->addFieldToFilter('source_model', 'eav/entity_attribute_source_table');
            foreach ($customerAttributes as $customerAttribute) {
                $attributeCode = $customerAttribute->getAttributeCode();
                if ($customer->getData($attributeCode)) {
                    try {
                        $attributeOptions = $customerAttribute->getSource()->getAllOptions();
                        foreach ($attributeOptions as $attributeOption) {
                            if ($attributeOption['value'] == $customer->getData($attributeCode)) {
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