<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-08-29T17:56:51+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Observer/Event.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Observer_Event extends Xtento_OrderExport_Model_Observer_Abstract
{
    private $_events = array();
    static $_exportedIds = array();

    /*
     * Event configuration
     */
    // Magento default events
    const EVENT_SALES_ORDER_SAVE_AFTER = 1;
    const EVENT_SALES_ORDER_PLACE_AFTER = 2;
    const EVENT_SALES_ORDER_PAYMENT_PLACE_END = 3;
    const EVENT_SALES_ORDER_INVOICE_REGISTER = 4;
    const EVENT_SALES_ORDER_INVOICE_PAY = 5;
    const EVENT_SALES_ORDER_SHIPMENT_SAVE_AFTER = 6;
    const EVENT_SALES_ORDER_CREDITMEMO_SAVE_AFTER = 7;
    // Customer events
    const EVENT_CUSTOMER_SAVE_AFTER = 20;
    const EVENT_CUSTOMER_AFTER_REGISTRATION = 21;
    const EVENT_CUSTOMER_ADDRESS_SAVE_AFTER = 22;
    // Third party events
    const EVENT_PRODUCTRETURN_ORDER_CREATED_FOR_RMA = 30;

    public function getEvents($entity = false, $allEvents = false)
    {
        $events = array();
        // Events where order information can be exported
        if ($allEvents || $entity == Xtento_OrderExport_Model_Export::ENTITY_ORDER) {
            $events[Xtento_OrderExport_Model_Export::ENTITY_ORDER][self::EVENT_SALES_ORDER_PLACE_AFTER] = array(
                'event' => 'sales_order_place_after',
                'label' => Mage::helper('xtento_orderexport')->__('After order creation (Event: sales_order_place_after)'),
                'method' => 'getOrder()',
                'force_collection_item' => true
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_ORDER][self::EVENT_SALES_ORDER_SAVE_AFTER] = array(
                'event' => 'sales_order_save_after',
                'label' => Mage::helper('xtento_orderexport')->__('After order modification (Event: sales_order_save_after)'),
                'method' => 'getOrder()',
                'force_collection_item' => true
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_ORDER][self::EVENT_SALES_ORDER_PAYMENT_PLACE_END] = array(
                'event' => 'sales_order_payment_place_end',
                'label' => Mage::helper('xtento_orderexport')->__('After order placement completed (Event: sales_order_payment_place_end)'),
                'method' => 'getPayment()->getOrder()',
                'force_collection_item' => true
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_ORDER][self::EVENT_SALES_ORDER_INVOICE_REGISTER] = array(
                'event' => 'sales_order_invoice_register',
                'label' => Mage::helper('xtento_orderexport')->__('After invoice creation (Event: sales_order_invoice_register)'),
                'method' => 'getInvoice()->getOrder()'
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_ORDER][self::EVENT_SALES_ORDER_INVOICE_PAY] = array(
                'event' => 'sales_order_invoice_pay',
                'label' => Mage::helper('xtento_orderexport')->__('After invoice has been paid (Event: sales_order_invoice_pay)'),
                'method' => 'getInvoice()->getOrder()'
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_ORDER][self::EVENT_SALES_ORDER_SHIPMENT_SAVE_AFTER] = array(
                'event' => 'sales_order_shipment_save_after',
                'label' => Mage::helper('xtento_orderexport')->__('After shipment creation (Event: sales_order_shipment_save_after)'),
                'method' => 'getShipment()->getOrder()'
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_ORDER][self::EVENT_SALES_ORDER_CREDITMEMO_SAVE_AFTER] = array(
                'event' => 'sales_order_creditmemo_save_after',
                'label' => Mage::helper('xtento_orderexport')->__('After credit memo creation (Event: sales_order_creditmemo_save_after)'),
                'method' => 'getCreditmemo()->getOrder()'
            );
        }
        // Events where invoice information can be exported
        if ($allEvents || $entity == Xtento_OrderExport_Model_Export::ENTITY_INVOICE) {
            $events[Xtento_OrderExport_Model_Export::ENTITY_INVOICE][self::EVENT_SALES_ORDER_INVOICE_REGISTER] = array(
                'event' => 'sales_order_invoice_register',
                'label' => Mage::helper('xtento_orderexport')->__('After invoice creation (Event: sales_order_invoice_register)'),
                'method' => 'getInvoice()'
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_INVOICE][self::EVENT_SALES_ORDER_INVOICE_PAY] = array(
                'event' => 'sales_order_invoice_pay',
                'label' => Mage::helper('xtento_orderexport')->__('After invoice has been paid (Event: sales_order_invoice_pay)'),
                'method' => 'getInvoice()'
            );
        }
        // Events where shipment information can be exported
        if ($allEvents || $entity == Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT) {
            $events[Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT][self::EVENT_SALES_ORDER_SHIPMENT_SAVE_AFTER] = array(
                'event' => 'sales_order_shipment_save_after',
                'label' => Mage::helper('xtento_orderexport')->__('After shipment creation (Event: sales_order_shipment_save_after)'),
                'method' => 'getShipment()'
            );
        }
        // Events where credit memo information can be exported
        if ($allEvents || $entity == Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO) {
            $events[Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO][self::EVENT_SALES_ORDER_CREDITMEMO_SAVE_AFTER] = array(
                'event' => 'sales_order_creditmemo_save_after',
                'label' => Mage::helper('xtento_orderexport')->__('After credit memo creation (Event: sales_order_creditmemo_save_after)'),
                'method' => 'getCreditmemo()'
            );
        }
        // Events where customer information can be exported
        if ($allEvents || $entity == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
            $events[Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER][self::EVENT_CUSTOMER_AFTER_REGISTRATION] = array(
                'event' => 'customer_register_success',
                'label' => Mage::helper('xtento_orderexport')->__('After customer signs up'),
                'method' => 'getCustomer()'
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER][self::EVENT_CUSTOMER_SAVE_AFTER] = array(
                'event' => 'customer_save_after',
                'label' => Mage::helper('xtento_orderexport')->__('After customer account gets modified'),
                'method' => 'getCustomer()'
            );
            $events[Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER][self::EVENT_CUSTOMER_ADDRESS_SAVE_AFTER] = array(
                'event' => 'customer_address_save_after',
                'label' => Mage::helper('xtento_orderexport')->__('After customer address gets modified'),
                'method' => 'getCustomerAddress()->getCustomer()'
            );
        }
        // Third party events
        if (Mage::helper('xtcore/utils')->isExtensionInstalled('MDN_ProductReturn') && ($allEvents || $entity == Xtento_OrderExport_Model_Export::ENTITY_ORDER)) {
            $events[Xtento_OrderExport_Model_Export::ENTITY_ORDER][self::EVENT_PRODUCTRETURN_ORDER_CREATED_FOR_RMA] = array(
                'event' => 'productreturn_order_created_for_rma',
                'label' => Mage::helper('xtento_orderexport')->__('productreturn_order_created_for_rma (Called by "MDN_ProductReturn" extension after RMA order placement)'),
                'method' => 'getOrder()'
            );
        }
        return $events;
    }

    /*
     * Add events below this line
     */
    public function sales_order_save_after(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_SAVE_AFTER, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
    }

    public function sales_order_place_after(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_PLACE_AFTER, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
    }

    public function sales_order_payment_place_end(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_PAYMENT_PLACE_END, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
    }

    public function sales_order_invoice_register(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_INVOICE_REGISTER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE);
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_INVOICE_REGISTER, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
    }

    public function sales_order_invoice_pay(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_INVOICE_PAY, Xtento_OrderExport_Model_Export::ENTITY_INVOICE);
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_INVOICE_PAY, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
    }

    public function sales_order_shipment_save_after(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_SHIPMENT_SAVE_AFTER, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT);
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_SHIPMENT_SAVE_AFTER, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
    }

    public function sales_order_creditmemo_save_after(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_CREDITMEMO_SAVE_AFTER, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO);
        $this->_handleEvent($observer, self::EVENT_SALES_ORDER_CREDITMEMO_SAVE_AFTER, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
    }

    /*
     * Customer events
     */
    public function customer_save_after_registration(Varien_Event_Observer $observer)
    {
        // Check if customer is new, only export then
        if (!$observer->getCustomer()->getOrigData()) {
            $this->_handleEvent($observer, self::EVENT_CUSTOMER_AFTER_REGISTRATION, Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER);
        }
    }

    public function customer_save_after(Varien_Event_Observer $observer)
    {
        // Check if customer is not new, only export then
        if ($observer->getCustomer()->getOrigData()) {
            $this->_handleEvent($observer, self::EVENT_CUSTOMER_SAVE_AFTER, Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER);
        }
    }

    public function customer_address_save_after(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_CUSTOMER_ADDRESS_SAVE_AFTER, Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER);
    }

    /*
     *  Third party events
     */
    public function productreturn_order_created_for_rma(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_PRODUCTRETURN_ORDER_CREATED_FOR_RMA, Xtento_OrderExport_Model_Export::ENTITY_ORDER);
    }

    /* For third party events calling the handleEvent function from outside this class */
    public function handleEvent(Varien_Event_Observer $observer, $eventId = 0, $entity)
    {
        $this->_handleEvent($observer, $eventId, $entity);
    }

    /*
     * Code handling events
     */
    private function _handleEvent(Varien_Event_Observer $observer, $eventId = 0, $entity)
    {
        try {
            if (!Mage::helper('xtento_orderexport')->getModuleEnabled() || !Mage::helper('xtento_orderexport')->isModuleProperlyInstalled()) {
                return;
            }
            if (Mage::registry('do_not_process_event_exports') === true) {
                return;
            }
            $event = $observer->getEvent();

            // Load profiles which are listening for this event
            $profileCollection = Mage::getModel('xtento_orderexport/profile')->getCollection()
                ->addFieldToFilter('enabled', 1) // Profile enabled
                ->addFieldToFilter('entity', $entity)
                ->addFieldToFilter('event_observers', array('like' => '%' . $eventId . '%')); // Event enabled "pre-check"
            foreach ($profileCollection as $profile) {
                $profileId = $profile->getId();
                $eventObservers = explode(",", $profile->getEventObservers());
                if (!in_array($eventId, $eventObservers)) {
                    continue; // Not enabled for this event
                }
                if (!isset(self::$_exportedIds[$profileId])) {
                    self::$_exportedIds[$profileId] = array();
                }
                $entityIdField = 'main_table.entity_id';
                if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                    $entityIdField = 'entity_id';
                }
                $exportObject = $this->_getExportObject($entity, $event, $eventId);
                if ($exportObject && !in_array($exportObject->getId(), self::$_exportedIds[$profileId])) {
                    $exportModel = Mage::getModel('xtento_orderexport/export', array('profile' => $profile));
                    if (isset($this->_events[$entity][$eventId]['force_collection_item']) && $this->_events[$entity][$eventId]['force_collection_item'] === true) {
                        $filters = $this->_addProfileFilters($profile);
                        if ($exportModel->eventExport($filters, $exportObject)) {
                            // Has been exported in this execution.. do not export again in the same execution.
                            if ($exportObject->getId()) {
                                array_push(self::$_exportedIds[$profileId], $exportObject->getId());
                            }
                            Mage::registry('export_log')->setExportEvent($this->_events[$entity][$eventId]['event'])->save();
                        }
                    } else if ($exportObject->getId()) {
                        $filters = array(array($entityIdField => $exportObject->getId()));
                        $filters = array_merge($filters, $this->_addProfileFilters($profile));
                        if ($exportModel->eventExport($filters)) {
                            // Has been exported in this execution.. do not export again in the same execution.
                            array_push(self::$_exportedIds[$profileId], $exportObject->getId());
                            Mage::registry('export_log')->setExportEvent($this->_events[$entity][$eventId]['event'])->save();
                        }
                    }
                } else {
                    Mage::log('Event handler for event ' . $eventId . ': Could not find export object.', 'xtento_orderexport_event.log', true);
                }
            }
        } catch (Exception $e) {
            Mage::log('Event handler exception for event ' . $eventId . ': ' . $e->getMessage(), null, 'xtento_orderexport_event.log', true);
            return;
        }
    }

    private function _getExportObject($entity, $event, $eventId)
    {
        if (empty($this->_events)) {
            $this->_events = $this->getEvents(false, true);
        }
        if (isset($this->_events[$entity][$eventId]) && isset($this->_events[$entity][$eventId]['method'])) {
            $eventMethods = explode("->", str_replace('()', '', $this->_events[$entity][$eventId]['method']));
            if (count($eventMethods) == 1) {
                return $event->{$eventMethods[0]}();
            } else if (count($eventMethods) == 2) {
                return $event->{$eventMethods[0]}()->{$eventMethods[1]}();
            }
        }
        return false;
    }
}