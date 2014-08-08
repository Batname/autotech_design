<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-05-05T22:09:03+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Observer/Abstract.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_OrderExport_Model_Observer_Abstract extends Mage_Core_Model_Abstract
{
    /*
     * Add store, date, status, ... filters based on profile settings
     */
    protected function _addProfileFilters($profile)
    {
        $filters = array();
        // Table prefix
        $tablePrefix = 'main_table.';
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.1', '<=')) {
            $tablePrefix = '';
        }
        // Filters
        $profileFilterStoreIds = explode(",", $profile->getStoreIds());
        if (!empty($profileFilterStoreIds)) {
            $storeIds = array();
            foreach ($profileFilterStoreIds as $storeId) {
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
        $profileFilterStatus = explode(",", $profile->getExportFilterStatus());
        if (!empty($profileFilterStatus)) {
            $statuses = array();
            foreach ($profileFilterStatus as $status) {
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
        $dateRangeFilter = array();
        $profileFilterDatefrom = $profile->getExportFilterDatefrom();
        if (!empty($profileFilterDatefrom)) {
            $dateRangeFilter['datetime'] = true;
            #$dateRangeFilter['from'] = sprintf('%s 00:00:00', $profileFilterDatefrom);
            $dateRangeFilter['from'] = Mage::helper('xtento_orderexport/date')->convertDate($profileFilterDatefrom)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            #$dateRangeFilter['to']->add(1, Zend_Date::HOUR);
        }
        $profileFilterDateto = $profile->getExportFilterDateto();
        if (!empty($profileFilterDateto)) {
            $dateRangeFilter['datetime'] = true;
            #$dateRangeFilter['to'] = sprintf('%s 23:59:59', $profileFilterDateto);
            #$dateRangeFilter['to'] = Mage::helper('xtento_orderexport/date')->convertDate($profileFilterDateto, false, true);
            $dateRangeFilter['to'] = Mage::helper('xtento_orderexport/date')->convertDate($profileFilterDateto /*, false, true*/);
            $dateRangeFilter['to']->add('1', Zend_Date::DAY);
            $dateRangeFilter['to'] = $dateRangeFilter['to']->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        $profileFilterCreatedLastXDays = $profile->getData('export_filter_last_x_days');
        if (!empty($profileFilterCreatedLastXDays) || $profileFilterCreatedLastXDays == '0') {
            $profileFilterCreatedLastXDays = intval(preg_replace('/[^0-9]/', '', $profileFilterCreatedLastXDays));
            if ($profileFilterCreatedLastXDays >= 0) {
                /*$dateToday = Mage::app()->getLocale()->date();
                $dateToday->sub($profileFilterCreatedLastXDays, Zend_Date::DAY);
                $dateRangeFilter['date'] = true;
                $dateRangeFilter['from'] = $dateToday->toString('yyyy-MM-dd 00:00:00');*/
                $dateToday = Zend_Date::now();
                $dateToday->sub($profileFilterCreatedLastXDays, Zend_Date::DAY);
                $dateToday->setHour(00);
                $dateToday->setSecond(00);
                $dateToday->setMinute(00);
                $dateToday->setLocale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
                $dateToday->setTimezone(Mage::getStoreConfig(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)); // Dates are stored in UTC
                $dateRangeFilter['datetime'] = true;
                $dateRangeFilter['from'] = $dateToday->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }
        }
        $profileFilterOlderThanXMinutes = $profile->getData('export_filter_older_x_minutes');
        if (!empty($profileFilterOlderThanXMinutes)) {
            $profileFilterOlderThanXMinutes = intval(preg_replace('/[^0-9]/', '', $profileFilterOlderThanXMinutes));
            if ($profileFilterOlderThanXMinutes > 0) {
                $dateToday = Zend_Date::now();
                $dateToday->sub($profileFilterOlderThanXMinutes, Zend_Date::MINUTE);
                $dateToday->setLocale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
                $dateToday->setTimezone(Mage::getStoreConfig(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)); // Dates are stored in UTC
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
        return $filters;
    }
}