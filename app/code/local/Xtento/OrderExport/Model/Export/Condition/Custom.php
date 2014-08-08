<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-09-08T16:10:39+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Condition/Custom.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Condition_Custom extends Mage_Core_Model_Abstract
{
    protected $_customAttributes = array();
    protected $_customNotMappedAttributes = array();
    protected $_omitAttributes = array();

    public function getCustomAttributes()
    {
        // Omitted attributes
        $this->_omitAttributes = array(
            'status' => 'Status',
            'state' => 'State',
        );
        // Custom ready to use attributes
        $attributes = array(
            'payment_method' => Mage::helper('salesrule')->__('Payment Method'),
            'shipping_method' => Mage::helper('salesrule')->__('Shipping Method'),
            'xt_billing_postcode' => Mage::helper('xtento_orderexport')->__('Billing Postcode'),
            'xt_billing_region' => Mage::helper('xtento_orderexport')->__('Billing Region'),
            'xt_billing_region_id' => Mage::helper('xtento_orderexport')->__('Billing State/Province'),
            'xt_billing_country_id' => Mage::helper('xtento_orderexport')->__('Billing Country'),
            'xt_shipping_postcode' => Mage::helper('salesrule')->__('Shipping Postcode'),
            'xt_shipping_region' => Mage::helper('salesrule')->__('Shipping Region'),
            'xt_shipping_region_id' => Mage::helper('salesrule')->__('Shipping State/Province'),
            'xt_shipping_country_id' => Mage::helper('salesrule')->__('Shipping Country'),
        );
        $this->_customAttributes = $attributes;
        return $attributes;
    }

    /*
     * Further attributes from this entity
     */
    public function getCustomNotMappedAttributes($type = '')
    {
        if (empty($this->_customAttributes)) {
            $this->_customAttributes = $this->getCustomAttributes();
        }
        if (!empty($this->_customNotMappedAttributes)) {
            return $this->_customNotMappedAttributes;
        }
        $entity = Mage::registry('order_export_profile')->getEntity();
        $resource = Mage::getSingleton('core/resource');
        $columns = array_keys($resource->getConnection('core_read')->describeTable($resource->getTableName('sales/' . $entity . $type)));
        sort($columns);
        /*$fields = Mage::getModel('sales/order')->getCollection();
        $fields->getSelect()->limit(1, 0);
        $fields->getSelect()->where('subtotal = 123');
        $fields = $fields->getData();
        $fields = array_keys($fields[0]);
        sort($fields);*/

        $attributes = array();
        foreach ($columns as $column) {
            if (isset($this->_customAttributes[$column]) || isset($this->_omitAttributes[$column])) {
                continue;
            }
            $attributes[$column] = $column;
        }
        $this->_customNotMappedAttributes = $attributes;
        return $attributes;
    }
}