<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-04-24T15:19:31+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Shared/Items.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Shared_Items extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    private $_origWriteArray;

    public function getConfiguration()
    {
        // Init cache
        if (!isset($this->_cache['product_attributes'])) {
            $this->_cache['product_attributes'] = array();
        }
        // Return config
        return array(
            'name' => 'Item information',
            'category' => 'Shared',
            'description' => 'Export ordered/invoiced/shipped/refunded items of entity.',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO, Xtento_OrderExport_Model_Export::ENTITY_QUOTE),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['items'];
        // Fetch fields to export
        $object = $collectionItem->getObject();
        #$order = $collectionItem->getOrder();
        $items = $object->getAllItems();
        if (empty($items) || (!$this->fieldLoadingRequired('items') && !$this->fieldLoadingRequired('tax_rates') && !$this->fieldLoadingRequired('packages/'))) {
            return $returnArray;
        }

        // Export item information
        $taxRates = array();
        $itemCount = 0;
        $totalQty = 0;
        foreach ($items as $item) {
            $orderItem = false;
            // Check if this product type should be exported
            if ($this->getProfile() && $item->getProductType() && in_array($item->getProductType(), explode(",", $this->getProfile()->getExportFilterProductType()))) {
                continue; // Product type should be not exported
            }
            if ($this->getProfile() && !$item->getProductType() && $this->getProfile()->getExportFilterProductType() !== '' && $entityType !== Xtento_OrderExport_Model_Export::ENTITY_ORDER && $entityType !== Xtento_OrderExport_Model_Export::ENTITY_QUOTE) {
                // We are not exporting orders, but need to check the product type - thus, need to load the order item.
                $orderItem = Mage::getModel('sales/order_item')->load($item->getOrderItemId());
                if ($orderItem->getProductType() && in_array($orderItem->getProductType(), explode(",", $this->getProfile()->getExportFilterProductType()))) {
                    continue; // Product type should be not exported
                }
            }
            // Export general item information
            $this->_writeArray = & $returnArray['items'][];
            $this->_origWriteArray = & $this->_writeArray;
            $itemCount++;
            if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_ORDER) {
                $itemQty = $item->getQtyOrdered();
            } else {
                $itemQty = $item->getQty();
            }
            $totalQty += $itemQty;
            $this->writeValue('qty_ordered', $itemQty); // Legacy
            $this->writeValue('qty', $itemQty);

            $this->writeValue('item_number', $itemCount);
            $this->writeValue('order_product_number', $itemCount); // Legacy
            foreach ($item->getData() as $key => $value) {
                if ($key == 'qty_ordered' || $key == 'qty') continue;
                $this->writeValue($key, $value);
            }

            // Stock level
            if ($this->fieldLoadingRequired('qty_in_stock')) {
                $stockLevel = 0;
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProductId());
                if ($stockItem->getId()) {
                    $stockLevel = $stockItem->getQty();
                }
                $this->writeValue('qty_in_stock', $stockLevel);
            }

            // Enterprise Gift Wrapping information
            if ($this->fieldLoadingRequired('enterprise_giftwrapping') && Mage::helper('xtcore/utils')->getIsPEorEE()) {
                if ($item->getGwId()) {
                    $this->_writeArray['enterprise_giftwrapping'] = array();
                    $this->_writeArray =& $this->_writeArray['enterprise_giftwrapping'];
                    $wrapping = Mage::getModel('enterprise_giftwrapping/wrapping')->load($item->getGwId());
                    if ($wrapping->getId()) {
                        foreach ($wrapping->getData() as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                        $this->writeValue('image_url', $wrapping->getImageUrl());
                    }
                }
            }

            // Repeat SKU by qty ordered, i.e. if "test" is ordered twice output test,test
            if ($this->fieldLoadingRequired('sku_repeated_by_qty')) {
                $this->writeValue('sku_repeated_by_qty', implode(",", array_fill(0, $itemQty, $item->getSku())));
            }

            // Add fields of order item for invoice exports
            $taxItem = false;
            if ($entityType !== Xtento_OrderExport_Model_Export::ENTITY_ORDER && $entityType !== Xtento_OrderExport_Model_Export::ENTITY_QUOTE && ($this->fieldLoadingRequired('order_item') || $this->fieldLoadingRequired('tax_rates'))) {
                $this->_writeArray['order_item'] = array();
                $this->_writeArray =& $this->_writeArray['order_item'];
                if ($item->getOrderItemId()) {
                    if (!$orderItem) {
                        $orderItem = Mage::getModel('sales/order_item')->load($item->getOrderItemId());
                    }
                    if ($orderItem->getId()) {
                        $taxItem = $orderItem;
                        foreach ($orderItem->getData() as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                    }
                }
                $this->_writeArray = & $this->_origWriteArray;
            } else {
                $taxItem = $item;
            }

            // Gift message
            if ($this->fieldLoadingRequired('gift_message')) {
                $giftMessageId = $item->getGiftMessageId();
                if (!$giftMessageId && $orderItem) {
                    $giftMessageId = $orderItem->getGiftMessageId();
                }
                $giftMessageModel = Mage::getModel('giftmessage/message')->load($giftMessageId);
                if ($giftMessageModel->getId()) {
                    $this->writeValue('gift_message_sender', $giftMessageModel->getSender());
                    $this->writeValue('gift_message_recipient', $giftMessageModel->getRecipient());
                    $this->writeValue('gift_message', $giftMessageModel->getMessage());
                } else {
                    $this->writeValue('gift_message_sender', '');
                    $this->writeValue('gift_message_recipient', '');
                    $this->writeValue('gift_message', '');
                }
            }

            // Get parent item
            $parentItem = $item->getParentItem();
            if (!$parentItem && $orderItem) {
                $parentItemId = $orderItem->getParentItemId();
                if ($parentItemId) {
                    $parentItem = Mage::getModel('sales/order_item')->load($parentItemId);
                }
            }

            // Get bundle price
            $productOptions = $item->getProductOptions();
            if ($parentItem && $parentItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                if (!isset($productOptions['bundle_selection_attributes']) && $parentItem) {
                    $productOptions = $parentItem->getProductOptions();
                }
                if (isset($productOptions['bundle_selection_attributes'])) {
                    $bundleOptions = unserialize($productOptions['bundle_selection_attributes']);
                    if (isset($bundleOptions['price'])) {
                        $this->writeValue('is_bundle', true);
                        $this->writeValue('bundle_price', $bundleOptions['price']);
                    }
                }
            }
            if ($this->fieldLoadingRequired('info_buyrequest') && $productOptions && isset($productOptions['info_buyRequest']) && is_array($productOptions['info_buyRequest'])) {
                $this->_writeArray['info_buyrequest'] = array();
                $this->_writeArray = & $this->_origWriteArray['info_buyrequest'];
                foreach ($productOptions['info_buyRequest'] as $productOptionKey => $productOptionValue) {
                    if (!is_array($productOptionKey) && !is_object($productOptionKey) && !is_array($productOptionValue) && !is_object($productOptionValue)) {
                        $this->writeValue($productOptionKey, $productOptionValue);
                    }
                }
                $this->_writeArray = & $this->_origWriteArray;
            }

            if ($item->getProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE && $this->fieldLoadingRequired('downloadable_links')) {
                $productOptions = $item->getProductOptions();
                if ($productOptions) {
                    if (isset($productOptions['links']) && is_array($productOptions['links'])) {
                        $this->_writeArray['downloadable_links'] = array();
                        $downloadableLinksCollection = Mage::getModel('downloadable/link')->getCollection()
                            ->addTitleToResult()
                            ->addFieldToFilter('`main_table`.link_id', array('in' => $productOptions['links']));
                        foreach ($downloadableLinksCollection as $downloadableLink) {
                            $this->_writeArray = & $this->_origWriteArray['downloadable_links'][];
                            foreach ($downloadableLink->getData() as $downloadableKey => $downloadableValue) {
                                $this->writeValue($downloadableKey, $downloadableValue);
                            }
                        }
                        $this->_writeArray = & $this->_origWriteArray;
                    }
                }
            }

            // Save tax information for order
            if ($taxItem && $item->getTaxAmount() > 0 && $taxItem->getTaxPercent() > 0) {
                $taxPercent = str_replace('.', '_', sprintf('%.4f', $taxItem->getTaxPercent()));
                if (!isset($taxRates[$taxPercent])) {
                    $taxRates[$taxPercent] = $item->getTaxAmount();
                } else {
                    $taxRates[$taxPercent] += $item->getTaxAmount();
                }
            }

            // Add fields of parent item
            if ($this->fieldLoadingRequired('parent_item') && $parentItem) {
                $this->_writeArray['parent_item'] = array();
                $this->_writeArray =& $this->_writeArray['parent_item'];
                $tempOrigArray = & $this->_writeArray;
                foreach ($parentItem->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
                // Export parent product options
                if ($this->fieldLoadingRequired('custom_options') && $options = $parentItem->getProductOptions()) {
                    $this->_writeCustomOptions($options, $this->_writeArray);
                }
                $this->_writeArray =& $tempOrigArray;
                if ($this->fieldLoadingRequired('product_attributes')) {
                    $this->_writeProductAttributes($object, $parentItem);
                }
            }
            $this->_writeArray = & $this->_origWriteArray;
            // @todo: add "child field" (of this data type) so product attribute export can be disabled to speed up the export
            // Export product attributes
            if ($this->fieldLoadingRequired('product_attributes')) {
                $this->_writeProductAttributes($object, $item);
            }

            $this->_writeArray = & $this->_origWriteArray;
            // Export product options
            if ($this->fieldLoadingRequired('custom_options') && $options = $item->getProductOptions()) {
                // Export custom options
                $this->_writeCustomOptions($options, $this->_origWriteArray);
                // Export $options["attributes_info"].. maybe?
            }

            // Sample code to get ugiftcert gift certificate information:
            /*
             $giftCerts = Mage::getModel('ugiftcert/cert')->getCollection()->addItemFilter($item->getId());
             if (count($giftCerts)) {
                foreach ($giftCerts as $giftCert) {
                    if (isset($giftCert['cert_number'])) {
                        ...
                    }
                }
             }
             */
        }

        // Sample code to add specific things/amounts as line items:
        /*if ($object->getGiftMessageId() > 0) {
            $giftMessage = Mage::helper('giftmessage/message')->getGiftMessage($object->getGiftMessageId());
            $returnArray['items'][] = array(
                'sku' => 'MESSAGE',
                'qty_ordered' => 1,
                'qty' => 1,
                'price' => 0,
                'discount_percent' => '0',
                'custom_options' => array('custom_option' => array('value' => $giftMessage->getMessage()))
            );
        }*/

        $this->_writeArray = & $returnArray;
        $this->writeValue('export_total_qty_ordered', $totalQty);

        // Add tax amounts of other fees to $taxRates
        // Shipping
        $shippingAmount = 0;
        $shippingTaxAmount = 0;
        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_ORDER) {
            $shippingAmount = $object->getData('base_shipping_amount');
            $shippingTaxAmount = $object->getData('base_shipping_tax_amount');
        }
        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_INVOICE) {
            $shippingAmount = $object->getOrder()->getData('base_shipping_invoiced');
            $shippingTaxAmount = $object->getOrder()->getData('base_shipping_tax_amount');
        }
        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO) {
            $shippingAmount = $object->getOrder()->getData('base_shipping_refunded');
            $shippingTaxAmount = $object->getOrder()->getData('base_shipping_tax_refunded');
        }
        if ($shippingAmount > 0 && $shippingTaxAmount > 0) {
            $taxPercent = round($shippingTaxAmount / $shippingAmount * 100);
            $taxPercent = str_replace('.', '_', sprintf('%.4f', $taxPercent));
            if (!isset($taxRates[$taxPercent])) {
                $taxRates[$taxPercent] = $shippingTaxAmount;
            } else {
                $taxRates[$taxPercent] += $shippingTaxAmount;
            }
        }
        // Cash on Delivery
        $codFee = 0;
        $codFeeTax = 0;
        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_ORDER) {
            $codFee = $object->getBaseCodFee();
            $codFeeTax = $object->getBaseCodTaxAmount();
        }
        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_INVOICE) {
            $codFee = $object->getOrder()->getData('base_cod_fee_invoiced');
            $codFeeTax = $object->getOrder()->getData('base_cod_tax_amount_invoiced');
        }
        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO) {
            $codFee = $object->getOrder()->getData('base_cod_fee_refunded');
            $codFeeTax = $object->getOrder()->getData('base_cod_tax_amount_refunded');
        }
        if ($codFee > 0 && $codFeeTax > 0) {
            $taxPercent = round($codFeeTax / $codFee * 100);
            $taxPercent = str_replace('.', '_', sprintf('%.4f', $taxPercent));
            if (!isset($taxRates[$taxPercent])) {
                $taxRates[$taxPercent] = $codFeeTax;
            } else {
                $taxRates[$taxPercent] += $codFeeTax;
            }
        }

        // At least provide a 0% tax rate if no tax was found, as no tax was charged then
        if (empty($taxRates)) {
            $taxRates = array('0_0000' => '');
        }

        // Special VAT Refund construct in the ls_vatrefund field, reset all VAT in that case
        if ($object->getData('ls_vatrefund') < 0) {
            $taxRates = array('0_0000' => '');
        }

        // Export tax information
        $this->_writeArray['tax_rates'] = array();
        if ($this->fieldLoadingRequired('tax_rates')) {
            foreach ($taxRates as $taxRate => $taxAmount) {
                $this->_writeArray = & $returnArray['tax_rates'][];
                $this->writeValue('rate', str_replace('_', '.', $taxRate));
                $this->writeValue('amount', $taxAmount);
            }
        }

        /*
        $packageCollection = Mage::getModel('shipusa/packages')->getCollection()->addQuoteFilter($object->getQuoteId());
        $packageCount = 0;
        $this->_writeArray['packages'] = array();
        foreach ($packageCollection as $package) {
            $packageCount++;
            $this->_writeArray = & $returnArray['packages'][];
            $this->writeValue('weight', $package->getWeight());
            $this->writeValue('counter', $packageCount);
        }
        */

        // Done
        return $returnArray;
    }

    private function _writeCustomOptions($options, &$writeArray)
    {
        if (isset($options['options'])) {
            $this->_writeArray['custom_options'] = array();
            foreach ($options['options'] as $customOption) {
                $optionValues = explode(",", $customOption['option_value']);
                $optionCount = 0;
                if (isset($customOption['option_type'])
                    && $customOption['option_type'] !== 'field'
                    && $customOption['option_type'] !== 'area') {
                    foreach ($optionValues as $optionValue) {
                        $values = Mage::getModel('catalog/product_option_value')->load($optionValue);
                        if ($values->getOptionId() && $values->getSku()) {
                            $optionCount++;
                            $this->_writeArray = & $writeArray['custom_options'][];
                            $this->writeValue('name', $customOption['label']);
                            $this->writeValue('value', $customOption['value']);
                            $this->writeValue('sku', $values->getSku());
                        }
                    }
                }
                if ($optionCount === 0) {
                    $this->_writeArray = & $writeArray['custom_options'][];
                    $this->writeValue('name', $customOption['label']);
                    $this->writeValue('value', $customOption['value']);
                    $this->writeValue('sku', '');
                }
                if (isset($customOption['option_value'])) {
                    $unserializedOptionValues = @unserialize($customOption['option_value']);
                    if ($unserializedOptionValues !== false) {
                        foreach ($unserializedOptionValues as $unserializedOptionKey => $unserializedOptionValue) {
                            $this->writeValue($unserializedOptionKey, $unserializedOptionValue);
                        }
                    }
                }
            }
        }
    }

    private function _writeProductAttributes($object, $item)
    {
        $this->_writeArray['product_attributes'] = array();
        $this->_writeArray = & $this->_writeArray['product_attributes'];
        if (isset($this->_cache['product_attributes'][$object->getStoreId()]) && isset($this->_cache['product_attributes'][$object->getStoreId()][$item->getProductId()])) {
            // "cached"
            foreach ($this->_cache['product_attributes'][$object->getStoreId()][$item->getProductId()] as $attributeCode => $value) {
                $this->writeValue($attributeCode, $value);
            }
        } else {
            $product = Mage::getModel('catalog/product')->setStoreId($object->getStoreId())->load($item->getProductId());
            if ($product->getId()) {
                foreach ($product->getAttributes(null, true) as $productAttribute) {
                    $attributeCode = $productAttribute->getAttributeCode();
                    // Handle attribute set name
                    if ($this->fieldLoadingRequired('attribute_set_name') && $productAttribute->getAttributeCode() == 'attribute_set_id') {
                        $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
                        $attributeSetModel->load($productAttribute->getFrontend()->getValue($product));
                        if ($attributeSetModel->getId()) {
                            $this->writeValue('attribute_set_name', $attributeSetModel->getAttributeSetName());
                            $this->_cache['product_attributes'][$object->getStoreId()][$item->getProductId()]['attribute_set_name'] = $attributeSetModel->getAttributeSetName();
                        }
                    }
                    if (!$this->fieldLoadingRequired($attributeCode) || $attributeCode == 'category_ids') {
                        continue;
                    }
                    $value = $productAttribute->getFrontend()->getValue($product);
                    if ($attributeCode == 'image' || $attributeCode == 'small_image' || $attributeCode == 'thumbnail') {
                        $this->writeValue($attributeCode . '_raw', $value);
                        $this->writeValue($attributeCode, Mage::getBaseUrl('media') . 'catalog/product/' . $value);
                        continue;
                    }
                    $this->writeValue($attributeCode, $value);
                    $this->_cache['product_attributes'][$object->getStoreId()][$item->getProductId()][$productAttribute->getAttributeCode()] = $value;
                }
                if ($this->fieldLoadingRequired('category_ids')) {
                    $categoryIds = "|" . implode("|", $product->getCategoryIds()) . "|";
                    $this->writeValue('category_ids', $categoryIds);
                    $this->_cache['product_attributes'][$object->getStoreId()][$item->getProductId()]['category_ids'] = $categoryIds;
                }
                if ($this->fieldLoadingRequired('category_names')) {
                    if ($product->getCategoryIds()) {
                        $categoryNames = array();
                        foreach ($product->getCategoryIds() as $categoryId) {
                            $category = Mage::getModel('catalog/category')->load($categoryId);
                            if ($category && $category->getId()) {
                                $categoryNames[] = $category->getName();
                            }
                        }
                        $categoryNames = "|" . implode("|", $categoryNames) . "|";
                        $this->writeValue('category_names', $categoryNames);
                        $this->_cache['product_attributes'][$object->getStoreId()][$item->getProductId()]['category_names'] = $categoryNames;
                    }
                }
            }
        }
    }
}