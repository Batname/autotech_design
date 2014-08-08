<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-01-11T17:41:15+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Observer/Massaction.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Observer_Massaction extends Mage_Core_Model_Abstract
{
    /**
     * Add mass-actions to the sales grids, the non-intrusive way.
     */
    public function core_block_abstract_prepare_layout_after($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (in_array($block->getRequest()->getControllerName(), $this->getControllerNames())) {
            $this->_addMassActions($block, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
            $this->_addMassActions($block, Xtento_OrderExport_Model_Export::ENTITY_INVOICE);
            $this->_addMassActions($block, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT);
            $this->_addMassActions($block, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO);
        }
    }

    private function _addMassActions($block, $type)
    {
        // @todo: Add option in configuration: Show each export option separately or with profile select dropdown (as it is now)
        if (($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction || $block instanceof Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction) && $this->_initBlocks() && in_array($block->getRequest()->getControllerName(), $this->getControllerNames($type))) {
            if (Mage::registry('moduleString') !== 'false') {
                return;
            }
            $isSecure = Mage::app()->getStore()->isCurrentlySecure() ? true : false;
            $block->addItem('xtento_' . $type . '_export', array(
                'label' => Mage::helper('xtento_orderexport')->__('Export ' . ucfirst($type) . 's'),
                'url' => Mage::app()->getStore()->getUrl('adminhtml/orderexport_manual/gridPost', array('_secure' => $isSecure, 'type' => $type)),
                'additional' => array(
                    'profile_id' => array(
                        'name' => 'profile_id',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('xtento_orderexport')->__('Profile'),
                        'values' => Mage::getModel('xtento_orderexport/system_config_source_export_profile')->toOptionArray(false, $type)
                    )
                )
            ));
        }
    }

    /*
     * Get controller names where the module is supposed to modify the block
     */
    private function getControllerNames($type = false)
    {
        $controllerNames = array();
        if (!$type || $type == Xtento_OrderExport_Model_Export::ENTITY_ORDER) {
            array_push($controllerNames, 'sales_order');
            array_push($controllerNames, 'adminhtml_sales_order');
            array_push($controllerNames, 'admin_sales_order');
            array_push($controllerNames, 'orderspro_order');
            array_push($controllerNames, 'OrderPreparation'); // MDN / Magento Embedded ERP
        }
        if (!$type || $type == Xtento_OrderExport_Model_Export::ENTITY_INVOICE) {
            array_push($controllerNames, 'sales_invoice');
            array_push($controllerNames, 'adminhtml_sales_invoice');
        }
        if (!$type || $type == Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT) {
            array_push($controllerNames, 'sales_shipment');
            array_push($controllerNames, 'adminhtml_sales_shipment');
        }
        if (!$type || $type == Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO) {
            array_push($controllerNames, 'sales_creditmemo');
            array_push($controllerNames, 'adminhtml_sales_creditmemo');
        }
        return $controllerNames;
    }

    private function _initBlocks()
    {
        if (!Mage::helper('xtento_orderexport')->getModuleEnabled() || !Mage::helper('xtento_orderexport')->isModuleProperlyInstalled()) {
            return false;
        }
        return true;
    }
}