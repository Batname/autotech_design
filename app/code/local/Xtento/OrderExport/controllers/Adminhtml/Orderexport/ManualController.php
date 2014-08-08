<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-05-05T22:11:52+02:00
 * File:          app/code/local/Xtento/OrderExport/controllers/Adminhtml/Orderexport/ManualController.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Adminhtml_OrderExport_ManualController extends Xtento_OrderExport_Controller_Abstract
{
    /*
     * Export from grid handler
     */
    public function gridPostAction()
    {
        $exportType = $this->getRequest()->getParam('type', false);
        if (!$exportType) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('Export type not specified.'));
            return $this->_redirectReferer();
        }
        $exportIds = $this->getRequest()->getPost($exportType . '_ids', false);
        if (!$exportIds) {
            // Alternate way, which would match full_stock_orders_order_ids for example
            $safeExportType = preg_replace("/[^A-Za-z0-9]/", "", $exportType);
            foreach ($this->getRequest()->getPost() as $k => $v) {
                if (preg_match("/" . $safeExportType . "_ids$/", $k)) {
                    $exportIds = $v;
                }
            }
        }
        if (!$exportIds) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('Please select objects to export.'));
            return $this->_redirectReferer();
        }
        $profileId = $this->getRequest()->getPost('profile_id', false);
        if (!$profileId) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('No export profile specified.'));
            return $this->_redirectReferer();
        }
        $profile = Mage::getModel('xtento_orderexport/profile')->load($profileId);
        // Export
        try {
            $beginTime = time();
            $exportedFiles = Mage::getModel('xtento_orderexport/export', array('profile_id' => $profileId))->gridExport($exportIds);
            $endTime = time();
            if ($profile->getStartDownloadManualExport()) {
                return $this->_prepareFileDownload($exportedFiles);
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_orderexport')->__('Export of %d %ss completed successfully in %d seconds. Click <a href="%s">here</a> to download exported files.', Mage::registry('export_log')->getRecordsExported(), $profile->getEntity(), ($endTime - $beginTime), Mage::helper('adminhtml')->getUrl('*/orderexport_log/download', array('id' => Mage::registry('export_log')->getId()))));
                if (Mage::registry('export_log')->getResult() !== Xtento_OrderExport_Model_Log::RESULT_SUCCESSFUL) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__(nl2br(Mage::registry('export_log')->getResultMessage())));
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('Error: %s', nl2br($e->getMessage())));
        }
        return $this->_redirectReferer();
    }

    /*
     * Manual export handler
     */
    public function manualPostAction()
    {
        $profileId = $this->getRequest()->getPost('profile_id');
        $profile = Mage::getModel('xtento_orderexport/profile')->load($profileId);
        if (!$profile->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('No profile selected or this profile does not exist anymore.'));
            return $this->_redirectReferer();
        }
        // Table prefix
        $tablePrefix = 'main_table.';
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.1', '<=')) {
            $tablePrefix = '';
        }
        // Prepare filters
        $filters = array();
        if ($this->getRequest()->getPost('store_id') !== NULL) {
            $storeIds = array();
            foreach ($this->getRequest()->getPost('store_id') as $storeId) {
                if ($storeId != '0' && $storeId != '') {
                    array_push($storeIds, $storeId);
                }
            }
            if (!empty($storeIds)) {
                if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                    $websiteIds = array();
                    foreach ($storeIds as $storeId) {
                        array_push($websiteIds, Mage::getModel('core/store')->load($storeId)->getWebsiteId());
                    }
                    $filters[] = array('website_id' => array('in' => $websiteIds));
                } else {
                    $filters[] = array($tablePrefix . 'store_id' => array('in' => $storeIds));
                }
            }
        }
        if ($this->getRequest()->getPost('status') !== NULL) {
            $statuses = array();
            foreach ($this->getRequest()->getPost('status') as $status) {
                if ($status !== '') {
                    array_push($statuses, $status);
                }
            }
            if (!empty($statuses)) {
                if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_ORDER) {
                    $filters[] = array($tablePrefix . 'status' => array('in' => $statuses));
                } else {
                    $filters[] = array($tablePrefix . 'state' => array('in' => $statuses));
                }
            }
        }
        if ($this->getRequest()->getPost('increment_from') !== NULL) {
            $collection = Mage::getModel(Mage::helper('xtento_orderexport/export')->getExportEntity($profile->getEntity()))->getCollection();
            if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_QUOTE) {
                $collection->addFieldToSelect('entity_id')
                    ->addFieldToFilter('entity_id', $this->getRequest()->getPost('increment_from'));
            } else if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                $collection->addAttributeToSelect('entity_id')
                    ->addAttributeToFilter('entity_id', $this->getRequest()->getPost('increment_from'));
            } else {
                $collection->addAttributeToSelect('entity_id')
                    ->addAttributeToFilter('increment_id', $this->getRequest()->getPost('increment_from'));
            }
            $object = $collection->getFirstItem();
            if ($object && $object->getId()) {
                if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                    $filters[] = array('entity_id' => array('from' => $object->getId()));
                } else {
                    if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.1', '<=')) {
                        $filters[] = array('entity_id' => array('from' => $object->getId()));
                    } else {
                        $filters[] = array($tablePrefix . 'entity_id' => array('from' => $object->getId()));
                    }
                }
            } else {
                if ($this->getRequest()->getPost('increment_from') != 1) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('The supplied starting increment_id does not exist. Use 1 to export from the beginning.'));
                    return $this->_redirect('*/orderexport_manual/index', array('profile_id' => $profile->getId()));
                }
            }
        }
        if ($this->getRequest()->getPost('increment_to') !== NULL) {
            $collection = Mage::getModel(Mage::helper('xtento_orderexport/export')->getExportEntity($profile->getEntity()))->getCollection();
            if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_QUOTE) {
                $collection->addFieldToSelect('entity_id')
                    ->addFieldToFilter('entity_id', $this->getRequest()->getPost('increment_to'));
            } else if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                $collection->addAttributeToSelect('entity_id')
                    ->addAttributeToFilter('entity_id', $this->getRequest()->getPost('increment_to'));
            } else {
                $collection->addAttributeToSelect('entity_id')
                    ->addAttributeToFilter('increment_id', $this->getRequest()->getPost('increment_to'));
            }
            $object = $collection->getFirstItem();
            if ($object && $object->getId()) {
                if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                    $filters[] = array('entity_id' => array('to' => $object->getId()));
                } else {
                    $filters[] = array($tablePrefix . 'entity_id' => array('to' => $object->getId()));
                }
            } else {
                if ($this->getRequest()->getPost('increment_to') != 0) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('The supplied ending increment_id does not exist. Use 0 to export until the end.'));
                    return $this->_redirect('*/orderexport_manual/index', array('profile_id' => $profile->getId()));
                }
            }
        }
        $dateRangeFilter = array();
        if ($this->getRequest()->getPost('daterange_from') != '') {
            $dateRangeFilter['datetime'] = true;
            $dateRangeFilter['from'] = Mage::helper('xtento_orderexport/date')->convertDate($this->getRequest()->getPost('daterange_from'))->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        if ($this->getRequest()->getPost('daterange_to') != '') {
            $dateRangeFilter['datetime'] = true;
            $dateRangeFilter['to'] = Mage::helper('xtento_orderexport/date')->convertDate($this->getRequest()->getPost('daterange_to') /*, false, true*/);
            $dateRangeFilter['to']->add('1', Zend_Date::DAY);
            $dateRangeFilter['to'] = $dateRangeFilter['to']->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        if ($this->getRequest()->getPost('last_x_days') != '') {
            $profileFilterCreatedLastXDays = $this->getRequest()->getPost('last_x_days');
            if (!empty($profileFilterCreatedLastXDays) || $profileFilterCreatedLastXDays == '0') {
                $profileFilterCreatedLastXDays = preg_replace('/[^0-9]/', '', $profileFilterCreatedLastXDays);
                if ($profileFilterCreatedLastXDays >= 0) {
                    #$dateToday = Mage::app()->getLocale()->date();
                    #$dateRangeFilter['date'] = true;
                    #$dateRangeFilter['from'] = $dateToday->toString('yyyy-MM-dd 00:00:00');
                    $dateToday = Zend_Date::now();
                    $dateToday->sub($profileFilterCreatedLastXDays, Zend_Date::DAY);
                    $dateToday->setHour(00);
                    $dateToday->setSecond(00);
                    $dateToday->setMinute(00);
                    $dateToday->setLocale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
                    $dateToday->setTimezone(Mage::getStoreConfig(Mage_Core_Model_Locale::DEFAULT_TIMEZONE));
                    $dateRangeFilter['datetime'] = true;
                    $dateRangeFilter['from'] = $dateToday->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                }
            }
        }
        $profileFilterOlderThanXMinutes = $profile->getData('export_filter_older_x_minutes');
        if (!empty($profileFilterOlderThanXMinutes)) {
            $profileFilterOlderThanXMinutes = intval(preg_replace('/[^0-9]/', '', $profileFilterOlderThanXMinutes));
            if ($profileFilterOlderThanXMinutes > 0) {
                $dateToday = Zend_Date::now();
                $dateToday->sub($profileFilterOlderThanXMinutes, Zend_Date::MINUTE);
                $dateToday->setLocale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
                $dateToday->setTimezone(Mage::getStoreConfig(Mage_Core_Model_Locale::DEFAULT_TIMEZONE));
                $dateRangeFilter['datetime'] = true;
                $dateRangeFilter['to'] = $dateToday->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }
        }
        if (!empty($dateRangeFilter)) {
            if ($profile->getEntity() == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
                $filters[] = array('created_at' => $dateRangeFilter);
            } else {
                $filters[] = array($tablePrefix . 'created_at' => $dateRangeFilter);
            }
        }
        #var_dump($filters);
        #die();
        // Export
        try {
            $beginTime = time();
            $exportModel = Mage::getModel('xtento_orderexport/export', array('profile' => $profile));
            if ($this->getRequest()->getPost('force_status') != '') {
                $exportModel->setForceChangeStatus($this->getRequest()->getPost('force_status'));
            }
            if ($this->getRequest()->getPost('filter_new_only') == 'on') {
                $exportModel->setExportFilterNewOnly(true);
            }
            $exportedFiles = $exportModel->manualExport($filters);
            $endTime = time();
            $successMessage = Mage::helper('xtento_orderexport')->__('Export of %d %ss completed successfully in %d seconds. Click <a href="%s">here</a> to download exported files.', Mage::registry('export_log')->getRecordsExported(), $profile->getEntity(), ($endTime - $beginTime), Mage::helper('adminhtml')->getUrl('*/orderexport_log/download', array('id' => Mage::registry('export_log')->getId())));
            if ($this->getRequest()->getPost('start_download', false)) {
                Mage::getModel('core/cookie')->set('fileDownload', 'true', null, '/', '', null, false);
                Mage::getModel('core/cookie')->set('lastMessage', $successMessage, null, '/', '', null, false);
                if (Mage::registry('export_log')->getResult() !== Xtento_OrderExport_Model_Log::RESULT_SUCCESSFUL) {
                    Mage::getModel('core/cookie')->set('lastErrorMessage', Mage::helper('xtento_orderexport')->__(nl2br(Mage::registry('export_log')->getResultMessage())), null, '/', '', null, false);
                } else {
                    Mage::getModel('core/cookie')->set('lastErrorMessage', '', null, '/', '', null, false);
                }
                return $this->_prepareFileDownload($exportedFiles);
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess($successMessage);
                if (Mage::registry('export_log')->getResult() !== Xtento_OrderExport_Model_Log::RESULT_SUCCESSFUL) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__(nl2br(Mage::registry('export_log')->getResultMessage())));
                }
                return $this->_redirect('*/orderexport_manual/index', array('profile_id' => $profile->getId()));
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_orderexport')->__('%s', nl2br($e->getMessage())));
            return $this->_redirect('*/orderexport_manual/index', array('profile_id' => $profile->getId()));
        }
    }

    /*
     * Manual export
     */
    public function indexAction()
    {
        if (!Xtento_OrderExport_Model_System_Config_Source_Order_Status::isEnabled() || !Mage::helper('xtento_orderexport')->getModuleEnabled()) {
            return $this->_redirect('*/orderexport_index/disabled');
        }
        $this->_initAction()->renderLayout();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/orderexport')
            ->_title(Mage::helper('xtento_orderexport')->__('Sales Export'))->_title(Mage::helper('xtento_orderexport')->__('Manual Export'));
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/orderexport/manual');
    }
}