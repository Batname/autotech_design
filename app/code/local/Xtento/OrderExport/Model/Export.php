<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-05-05T22:12:16+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export extends Mage_Core_Model_Abstract
{
    /*
     * The actual export model handling object (order/invoice/shipment/creditmemo/quote) exports
     */
    protected $_orderStates = array();
    protected $_stateStatuses = array();

    // Export entities
    const ENTITY_CUSTOMER = 'customer';
    const ENTITY_ORDER = 'order';
    const ENTITY_INVOICE = 'invoice';
    const ENTITY_SHIPMENT = 'shipment';
    const ENTITY_CREDITMEMO = 'creditmemo';
    const ENTITY_QUOTE = 'quote'; // Experimental

    // Export types
    const EXPORT_TYPE_TEST = 0; // Test Export
    const EXPORT_TYPE_GRID = 1; // Grid Export
    const EXPORT_TYPE_MANUAL = 2; // From "Manual Export" screen
    const EXPORT_TYPE_CRONJOB = 3; // Cronjob Export
    const EXPORT_TYPE_EVENT = 4; // Export after event

    public function _construct()
    {
        if ($this->getProfileId()) {
            $profile = Mage::getModel('xtento_orderexport/profile')->load($this->getProfileId());
            $this->setProfile($profile);
        }
        parent::_construct();
    }

    public function getEntities()
    {
        $values = array();
        $values[Xtento_OrderExport_Model_Export::ENTITY_ORDER] = Mage::helper('xtento_orderexport')->__('Orders');
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
            $values[Xtento_OrderExport_Model_Export::ENTITY_INVOICE] = Mage::helper('xtento_orderexport')->__('Invoices');
            $values[Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT] = Mage::helper('xtento_orderexport')->__('Shipments');
            $values[Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO] = Mage::helper('xtento_orderexport')->__('Credit Memos');
            $values[Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER] = Mage::helper('xtento_orderexport')->__('Customers');
            if ($this->_getExperimentalFeatureSupport()) {
                $values[Xtento_OrderExport_Model_Export::ENTITY_QUOTE] = Mage::helper('xtento_orderexport')->__('Quotes');
            }
        }
        return $values;
    }

    public function getExportTypes()
    {
        $values = array();
        $values[Xtento_OrderExport_Model_Export::EXPORT_TYPE_TEST] = Mage::helper('xtento_orderexport')->__('Test Export');
        $values[Xtento_OrderExport_Model_Export::EXPORT_TYPE_MANUAL] = Mage::helper('xtento_orderexport')->__('Manual Export');
        $values[Xtento_OrderExport_Model_Export::EXPORT_TYPE_GRID] = Mage::helper('xtento_orderexport')->__('Grid Export');
        $values[Xtento_OrderExport_Model_Export::EXPORT_TYPE_CRONJOB] = Mage::helper('xtento_orderexport')->__('Cronjob Export');
        $values[Xtento_OrderExport_Model_Export::EXPORT_TYPE_EVENT] = Mage::helper('xtento_orderexport')->__('Event Export');
        return $values;
    }

    /*
     * Validate XSL Template function used to run a test export when editing a profile
     */
    public function testExport($exportId = false)
    {
        if (empty($exportId)) {
            return Mage::helper('xtento_orderexport')->__('No test ID to export specified.');
        }
        $this->setExportType(self::EXPORT_TYPE_TEST);
        Mage::register('is_test_orderexport', true, true);
        $filters[] = array('increment_id' => array('in' => explode(",", $exportId)));
        $exportedFiles = $this->_runExport($filters);
        return $exportedFiles;
    }

    public function gridExport($exportIds)
    {
        if (empty($exportIds)) {
            Mage::throwException(Mage::helper('xtento_orderexport')->__('No %ss to export specified.', $this->getProfile()->getEntity()));
        }
        $this->_checkStatus();
        $this->setExportType(self::EXPORT_TYPE_GRID);
        $this->_beforeExport();
        $filters[] = array('entity_id' => array('in' => $exportIds));
        $generatedFiles = $this->_runExport($filters);
        if ($this->getProfile()->getSaveFilesManualExport()) {
            $this->_saveFiles();
        }
        $this->_afterExport();
        return $generatedFiles;
    }

    public function manualExport($filters)
    {
        $this->_checkStatus();
        $this->setExportType(self::EXPORT_TYPE_MANUAL);
        $this->_beforeExport();
        $generatedFiles = $this->_runExport($filters);
        if ($this->getProfile()->getSaveFilesManualExport()) {
            $this->_saveFiles();
        }
        $this->_afterExport();
        return $generatedFiles;
    }

    public function eventExport($filters, $forcedCollectionItem = false)
    {
        $this->setExportType(self::EXPORT_TYPE_EVENT);
        $this->_beforeExport();
        $generatedFiles = $this->_runExport($filters, $forcedCollectionItem);
        if (empty($generatedFiles)) {
            $this->getLogEntry()->delete();
            return false;
        }
        $this->_saveFiles();
        $this->_afterExport();
        return true;
    }

    public function cronExport($filters)
    {
        $this->setExportType(self::EXPORT_TYPE_CRONJOB);
        $this->_beforeExport();
        $generatedFiles = $this->_runExport($filters);
        if (empty($generatedFiles)) {
            $this->getLogEntry()->delete();
            return false;
        }
        $this->_saveFiles();
        $this->_afterExport();
        return true;
    }

    private function _runExport($filters, $forcedCollectionItem = false)
    {
        try {
            set_time_limit(0);
            if (!$this->getProfile()) {
                Mage::throwException(Mage::helper('xtento_orderexport')->__('No profile to export specified.'));
            }
            $returnArray = $this->_exportObjects($filters, $forcedCollectionItem);
            if (empty($returnArray)) {
                Mage::throwException(Mage::helper('xtento_orderexport')->__('0 %ss have been exported.', $this->getProfile()->getEntity()));
            }
            $this->setReturnArrayWithObjects($returnArray);
            // Get output type
            if ($this->getProfile()->getOutputType() == 'csv') {
                $type = 'csv';
            } else if ($this->getProfile()->getOutputType() == 'xml') {
                $type = 'xml';
            } else {
                $type = 'xsl';
            }
            // Convert data
            if ($this->getProfile()->getExportOneFilePerObject()) {
                // Create one file per exported object
                $generatedFiles = array();
                foreach ($this->getReturnArrayWithObjects() as $returnObject) {
                    $generatedFiles = array_merge(
                        $generatedFiles,
                        Mage::getModel('xtento_orderexport/output_' . $type, array('profile' => $this->getProfile()))->convertData(array($returnObject))
                    );
                }
            } else {
                // Create just one file for all exported objects
                $generatedFiles = Mage::getModel('xtento_orderexport/output_' . $type, array('profile' => $this->getProfile()))->convertData($this->getReturnArrayWithObjects());
            }
            $this->setGeneratedFiles($generatedFiles);
            if (is_array($this->getReturnArrayWithObjects()) && $this->getLogEntry()) {
                $this->getLogEntry()->setRecordsExported(count($this->getReturnArrayWithObjects()));
            }
            return $generatedFiles;
        } catch (Exception $e) {
            if ($this->getLogEntry()) {
                $result = Xtento_OrderExport_Model_Log::RESULT_FAILED;
                if (preg_match('/have been exported/', $e->getMessage())) {
                    if ($this->getExportType() == self::EXPORT_TYPE_MANUAL || $this->getExportType() == self::EXPORT_TYPE_GRID) {
                        $result = Xtento_OrderExport_Model_Log::RESULT_WARNING;
                    } else {
                        return array();
                    }
                }
                $this->getLogEntry()->setResult($result);
                $this->getLogEntry()->addResultMessage($e->getMessage());
                $this->_afterExport();
            }
            if ($this->getExportType() == self::EXPORT_TYPE_MANUAL || $this->getExportType() == self::EXPORT_TYPE_GRID || $this->getExportType() == self::EXPORT_TYPE_TEST) {
                Mage::throwException($e->getMessage());
            }
            return array();
        }
    }

    private function _exportObjects($filters, $forcedCollectionItem = false)
    {
        $export = Mage::getModel('xtento_orderexport/export_entity_' . $this->getProfile()->getEntity());
        $export->setExportType($this->getExportType());
        $collection = $export->setCollectionFilters($filters);
        if ($this->getProfile()->getExportFilterNewOnly() && ($this->getExportType() == self::EXPORT_TYPE_CRONJOB || $this->getExportType() == self::EXPORT_TYPE_EVENT)) {
            $this->_addExportOnlyNewFilter($collection);
            $export->addExportOnlyNewFilter();
        }
        if ($this->getExportFilterNewOnly() && ($this->getExportType() == self::EXPORT_TYPE_MANUAL /* || $this->getExportType() == self::EXPORT_TYPE_GRID*/)) {
            $this->_addExportOnlyNewFilter($collection);
        }
        #var_dump($filters);
        #echo $collection->getSelect();
        #echo $collection->count(); die();
        $export->setProfile($this->getProfile());
        return $export->runExport($forcedCollectionItem);
    }

    private function _addExportOnlyNewFilter($collection)
    {
        $joinTable = ($this->getProfile()->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) ? 'e' : 'main_table';
        // Filter and hide objects that have been exported previously
        $collection->getSelect()->joinLeft(
            array('export_history' => $collection->getTable('xtento_orderexport/history')),
            $joinTable . '.entity_id = export_history.entity_id and ' . $collection->getConnection()->quoteInto('export_history.entity = ?', $this->getProfile()->getEntity()) . ' and ' . $collection->getConnection()->quoteInto('export_history.profile_id = ?', $this->getProfile()->getId()),
            array()
        );
        $collection->getSelect()->where('export_history.entity_id IS NULL');
        #echo $collection->getSelect(); die();
    }

    /*
     * Save files on their destinations
     */
    private function _saveFiles()
    {
        try {
            foreach ($this->getProfile()->getDestinations() as $destination) {
                try {
                    $savedFiles = $destination->saveFiles($this->getGeneratedFiles());
                    if (is_array($this->getFiles()) && is_array($savedFiles)) {
                        $this->setFiles(array_merge($this->getFiles(), $savedFiles));
                    } else {
                        $this->setFiles($savedFiles);
                    }
                } catch (Exception $e) {
                    $this->getLogEntry()->setResult(Xtento_OrderExport_Model_Log::RESULT_WARNING);
                    $this->getLogEntry()->addResultMessage($e->getMessage());
                }
            }
        } catch (Exception $e) {
            $this->getLogEntry()->setResult(Xtento_OrderExport_Model_Log::RESULT_FAILED);
            $this->getLogEntry()->addResultMessage($e->getMessage());
            if ($this->getExportType() == self::EXPORT_TYPE_MANUAL) {
                Mage::throwException($e->getMessage());
            }
        }
    }

    private function _beforeExport()
    {
        $this->setBeginTime(time());
        #$memBefore = memory_get_usage();
        #$timeBefore = time();
        #echo "Before export: " . $memBefore . " bytes / Time: " . $timeBefore . "<br>";
        $logEntry = Mage::getModel('xtento_orderexport/log');
        $logEntry->setCreatedAt(now());
        $logEntry->setProfileId($this->getProfile()->getId());
        $logEntry->setDestinationIds($this->getProfile()->getDestinationIds());
        $logEntry->setExportType($this->getExportType());
        $logEntry->setRecordsExported(0);
        $logEntry->setResultMessage(Mage::helper('xtento_orderexport')->__('Export started...'));
        $logEntry->save();
        $this->setLogEntry($logEntry);
        Mage::unregister('export_log');
        Mage::unregister('order_export_profile');
        Mage::register('export_log', $logEntry);
        Mage::register('order_export_profile', $this->getProfile());
    }

    private function _afterExport()
    {
        if ($this->getLogEntry()->getResult() !== Xtento_OrderExport_Model_Log::RESULT_FAILED) {
            $this->_invoiceShipOrder();
            $this->_adjustOrderStatus();
            $this->_cancelOrder();
            #$this->_createExportHistoryEntries();
            if ($this->getProfile()->getExportFilterNewOnly() || $this->getExportFilterNewOnly()) {
                $this->_createExportHistoryEntries();
            }
        }
        $this->_saveLog();
        Mage::unregister('order_export_profile');
        #echo "After export: " . memory_get_usage() . " (Difference: " . round((memory_get_usage() - $memBefore) / 1024 / 1024, 2) . " MB, " . (time() - $timeBefore) . " Secs) - Count: " . (count($exportIds)) . " -  Per entry: " . round(((memory_get_usage() - $memBefore) / 1024 / 1024) / (count($exportIds)), 2) . "<br>";
    }

    private function _createExportHistoryEntries()
    {
        if ($this->getReturnArrayWithObjects()) {
            // Save exported object ids in the export history
            foreach ($this->getReturnArrayWithObjects() as $object) {
                $historyEntry = Mage::getModel('xtento_orderexport/history');
                $historyEntry->setProfileId($this->getProfile()->getId());
                $historyEntry->setLogId($this->getLogEntry()->getId());
                $historyEntry->setEntity($this->getProfile()->getEntity());
                $historyEntry->setEntityId($object['entity_id']);
                $historyEntry->setExportedAt(now());
                $historyEntry->save();
            }
        }
    }

    private function _adjustOrderStatus()
    {
        if ($this->getProfile()->getEntity() == self::ENTITY_ORDER) {
            if (($this->getProfile()->getExportActionChangeStatus() !== '' || $this->getForceChangeStatus() !== NULL) && ($this->getExportType() == self::EXPORT_TYPE_MANUAL || $this->getExportType() == self::EXPORT_TYPE_GRID)) {
                if ($this->getForceChangeStatus() !== 'no_change') {
                    if ($this->getForceChangeStatus() !== NULL) {
                        $this->_changeOrderStatus($this->getForceChangeStatus());
                    } else {
                        $this->_changeOrderStatus($this->getProfile()->getExportActionChangeStatus());
                    }
                }
            }
            if ($this->getProfile()->getExportActionChangeStatus() !== '' && ($this->getExportType() == self::EXPORT_TYPE_EVENT || $this->getExportType() == self::EXPORT_TYPE_CRONJOB)) {
                $this->_changeOrderStatus($this->getProfile()->getExportActionChangeStatus());
            }
        }
    }

    private function _invoiceShipOrder()
    {
        if ($this->getProfile()->getEntity() == self::ENTITY_ORDER) {
            $returnArray = $this->getReturnArrayWithObjects();
            if (empty($returnArray)) {
                return;
            }
            if (!$this->getProfile()->getExportActionInvoiceOrder() && !$this->getProfile()->getExportActionShipOrder()) {
                return;
            }
            $doNotifyInvoice = $this->getProfile()->getExportActionInvoiceNotify();
            $doNotifyShipment = $this->getProfile()->getExportActionShipNotify();
            foreach ($returnArray as $object) {
                try {
                    $order = Mage::getModel('sales/order')->load($object['entity_id']);
                    if (!$order->getId()) {
                        continue;
                    }
                    Mage::register('do_not_process_event_exports', true, true);
                    // Invoice order
                    if ($this->getProfile()->getExportActionInvoiceOrder() && $order->canInvoice()) {
                        /** @var $invoice Mage_Sales_Model_Order_Invoice */
                        $invoice = $order->prepareInvoice();
                        if ($invoice->canCapture()) {
                            // Capture order online
                            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                        } else {
                            // Set invoice status to Paid
                            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                        }
                        $invoice->register();
                        $invoice->setEmailSent($doNotifyInvoice);

                        $invoice->getOrder()->setIsInProcess(true);

                        $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder())
                            ->save();

                        if ($doNotifyInvoice) $invoice->sendEmail($doNotifyInvoice, '');
                        unset($invoice);
                    }
                    // Just send the invoice email even though the order has been invoiced already
                    /*if ($doNotifyInvoice && !$order->canInvoice()) {
                        $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                            ->setOrderFilter($order)
                            ->addAttributeToSelect('entity_id')
                            ->addAttributeToSort('entity_id', 'desc')
                            ->setPage(1, 1);
                        $lastInvoice = $invoices->getFirstItem();
                        if ($lastInvoice->getId()) {
                            $lastInvoice = Mage::getModel('sales/order_invoice')->load($lastInvoice->getId());
                            if (!$lastInvoice->getEmailSent()) {
                                $lastInvoice->setEmailSent(true);
                                $lastInvoice->sendEmail(true, '');
                                $lastInvoice->save();
                            }
                        }
                    }*/
                    // Ship order
                    if ($this->getProfile()->getExportActionShipOrder() && $order->canShip()) {
                        $shipment = $order->prepareShipment();
                        $shipment->register();
                        $shipment->setEmailSent($doNotifyShipment);
                        $shipment->getOrder()->setIsInProcess(true);

                        $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($shipment)
                            ->addObject($shipment->getOrder())
                            ->save();

                        if ($doNotifyShipment) $shipment->sendEmail($doNotifyShipment, '');
                        unset($shipment);
                    }
                    // Just send the invoice email even though the order has been invoiced already
                    /*if ($doNotifyShipment && !$order->canShip()) {
                        $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                            ->setOrderFilter($order)
                            ->addAttributeToSelect('entity_id')
                            ->addAttributeToSort('entity_id', 'desc')
                            ->setPage(1, 1);
                        $lastShipment = $shipments->getFirstItem();
                        if ($lastShipment->getId()) {
                            $lastShipment = Mage::getModel('sales/order_shipment')->load($lastShipment->getId());
                            if (!$lastShipment->getEmailSent()) {
                                $lastShipment->setEmailSent(true);
                                $lastShipment->sendEmail(true, '');
                                $lastShipment->save();
                            }
                        }
                    }*/
                    Mage::unregister('do_not_process_event_exports');
                } catch (Exception $e) {
                    Mage::unregister('do_not_process_event_exports');
                    Mage::log('Exception catched while invoicing/shipping order id ' . $object['entity_id'] . ': ' . $e->getMessage(), null, 'xtento_orderexport_error.log', true);
                    continue;
                }
            }
        }
    }

    private function _changeOrderStatus($newStatus)
    {
        if ($newStatus == '') {
            Mage::throwException('No status to set for orders specified.');
        }
        $returnArray = $this->getReturnArrayWithObjects();
        if (empty($returnArray)) {
            return;
        }
        foreach ($returnArray as $object) {
            try {
                $order = Mage::getModel('sales/order')->load($object['entity_id']);
                if ($order->getId()) {
                    if ($order->getStatus() !== $newStatus) {
                        $this->_setOrderState($order, $newStatus);
                        $order->setStatus($newStatus);
                        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                            $order->addStatusHistoryComment('', $order->getStatus())->setIsCustomerNotified(0);
                        } else {
                            // 1.3 compatibility
                            $order->addStatusToHistory($order->getStatus());
                        }
                        // Compatibility fix for Amasty_OrderStatus
                        $statusModel = Mage::registry('amorderstatus_history_status');
                        if (($statusModel && $statusModel->getNotifyByEmail()) || Mage::registry('advancedorderstatus_notifications')) {
                            $order->sendOrderUpdateEmail();
                        }
                        // End
                        $order->save();
                    }
                }
            } catch (Exception $e) {
                Mage::log('Exception catched while changing order status for order id ' . $object['entity_id'] . ': ' . $e->getMessage(), null, 'xtento_orderexport_error.log', true);
                continue;
            }
        }
    }

    /*
     * Cancel exported orders, if enable in profile
     */
    private function _cancelOrder()
    {
        if (!$this->getProfile()->getExportActionCancelOrder()) {
            return $this;
        }
        $returnArray = $this->getReturnArrayWithObjects();
        if (empty($returnArray)) {
            return $this;
        }
        foreach ($returnArray as $object) {
            try {
                $order = Mage::getModel('sales/order')->load($object['entity_id']);
                if ($order->getId()) {
                    $order->cancel()->save();
                }
            } catch (Exception $e) {
                Mage::log('Exception catched while cancelling order id ' . $object['entity_id'] . ': ' . $e->getMessage(), null, 'xtento_orderexport_error.log', true);
                continue;
            }
        }
        return $this;
    }

    private function _setOrderState($order, $newOrderStatus)
    {
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.5.0.0', '>=')) {
            if (!isset($this->_orderStates)) {
                $this->_orderStates = Mage::getModel('sales/order_config')->getStates();
            }
            foreach ($this->_orderStates as $state => $label) {
                if (!isset($this->_stateStatuses[$state])) {
                    $this->_stateStatuses[$state] = Mage::getModel('sales/order_config')->getStateStatuses($state, false);
                }
                foreach ($this->_stateStatuses[$state] as $status) {
                    if ($status == $newOrderStatus) {
                        $order->setData('state', $state);
                        return;
                    }
                }
            }
        }
    }

    private function _saveLog()
    {
        $this->getProfile()->setLastExecution(now())->save();
        if (is_array($this->getFiles())) {
            $this->getLogEntry()->setFiles(implode("|", $this->getFiles()));
        }
        $this->getLogEntry()->setResult($this->getLogEntry()->getResult() ? $this->getLogEntry()->getResult() : Xtento_OrderExport_Model_Log::RESULT_SUCCESSFUL);
        $this->getLogEntry()->setResultMessage($this->getLogEntry()->getResultMessages() ? $this->getLogEntry()->getResultMessages() : Mage::helper('xtento_orderexport')->__('Export of %d %ss finished in %d seconds.', $this->getLogEntry()->getRecordsExported(), $this->getProfile()->getEntity(), (time() - $this->getBeginTime())));
        $this->getLogEntry()->save();
        $this->_errorEmailNotification();
        #Mage::unregister('export_log');
    }

    private function _errorEmailNotification()
    {
        if (!Mage::helper('xtento_orderexport')->isDebugEnabled() || Mage::helper('xtento_orderexport')->getDebugEmail() == '') {
            return $this;
        }
        if ($this->getLogEntry()->getResult() >= Xtento_OrderExport_Model_Log::RESULT_WARNING) {
            try {
                $mail = new Zend_Mail();
                $mail->setFrom('store@' . @$_SERVER['SERVER_NAME'], @$_SERVER['SERVER_NAME']);
                $mail->addTo(Mage::helper('xtento_orderexport')->getDebugEmail(), Mage::helper('xtento_orderexport')->getDebugEmail());
                $mail->setSubject('Magento Order Export Module @ ' . @$_SERVER['SERVER_NAME']);
                $mail->setBodyText('Warning/Error/Message(s): ' . $this->getLogEntry()->getResultMessages());
                if (Mage::helper('xtcore/utils')->isExtensionInstalled('Aschroder_SMTPPro') && Mage::helper('smtppro')->isEnabled()) {
                    // SMTPPro extension
                    $mail->send(Mage::helper('smtppro')->getTransport());
                } else {
                    $mail->send();
                }
            } catch (Exception $e) {
                $this->getLogEntry()->addResultMessage('Exception: ' . $e->getMessage());
                $this->getLogEntry()->setResult(Xtento_OrderExport_Model_Log::RESULT_WARNING);
                $this->getLogEntry()->setResultMessage($this->getLogEntry()->getResultMessages());
                $this->getLogEntry()->save();
            }
        }
        return $this;
    }

    private function _checkStatus()
    {
        if (!Xtento_OrderExport_Model_System_Config_Source_Order_Status::isEnabled()) {
            Mage::throwException(Mage::helper('xtento_orderexport')->getMsg());
        }
    }

    private function _getExperimentalFeatureSupport()
    {
        $experimentalFeatureDataFile = Mage::helper('xtcore/filesystem')->getModuleDir($this) . DS . 'xtento' . DS . 'experimental_features.xml';
        if (@file_exists($experimentalFeatureDataFile)) {
            return true;
        }
        return false;
    }
}