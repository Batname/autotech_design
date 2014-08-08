<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-05-06T19:38:51+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Entity/Abstract.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_OrderExport_Model_Export_Entity_Abstract extends Mage_Core_Model_Abstract
{
    protected $_collection;
    protected $_exportOnlyNewFilter = false;
    private $_returnArray = array();

    protected function _construct()
    {
        parent::_construct();
    }

    protected function _runExport($forcedCollectionItem = false)
    {
        $exportFields = array();
        if (Mage::helper('xtento_orderexport')->getFieldsTabEnabled()) {
            if ($this->getProfile()->getExportFields() !== '') {
                $exportFields = explode(",", $this->getProfile()->getExportFields());
            }
        }
        // Get validation profile
        /* Alternative approach if conditions check fails, we've seen this happening in a 1.5.0.1 installation, the profile conditions were simply empty and the profile needed to be loaded again: */
        $validationProfile = $this->getProfile();
        $exportConditions = $validationProfile->getData('conditions_serialized');
        if (strlen($exportConditions) > 90) {
            // Force load profile for rule validation, as it fails on some stores if the profile is not re-loaded
            $validationProfile = Mage::getModel('xtento_orderexport/profile')->load($this->getProfile()->getId());
        }
        // Reset export classes
        Mage::getSingleton('xtento_orderexport/export_data')->resetExportClasses();
        // Get export fields
        if ($forcedCollectionItem === false) {
            $collectionCount = null;
            $currItemNo = 1;
            $originalCollection = $this->_collection;
            $currPage = 1;
            $lastPage = 0;
            $break = false;
            while ($break !== true) {
                $collection = clone $originalCollection;
                $collection->setPageSize(100);
                $collection->setCurPage($currPage);
                $collection->load();
                if (is_null($collectionCount)) {
                    $collectionCount = $collection->getSize();
                    $lastPage = $collection->getLastPageNumber();
                }
                if ($currPage == $lastPage) {
                    $break = true;
                }
                $currPage++;
                foreach ($collection as $collectionItem) {
                    if ($this->getExportType() == Xtento_OrderExport_Model_Export::EXPORT_TYPE_TEST || $validationProfile->validate($collectionItem)) {
                        $returnData = $this->_exportData(new Xtento_OrderExport_Model_Export_Entity_Collection_Item($collectionItem, $this->_entityType, $currItemNo, $collectionCount), $exportFields);
                        if (!empty($returnData)) {
                            $this->_returnArray[] = $returnData;
                            $currItemNo++;
                        }
                    }
                }
            }
        } else {
            $rawFilters = $this->getRawCollectionFilters();
            $collectionItemValidated = true;
            // Manually check collection filters against collection item as there is no real collection
            if (is_array($rawFilters)) {
                foreach ($rawFilters as $filter) {
                    foreach ($filter as $filterField => $filterCondition) {
                        $filterField = str_replace("main_table.", "", $filterField);
                        $itemData = $forcedCollectionItem->getData($filterField);
                        foreach ($filterCondition as $filterConditionType => $acceptedValues) {
                            if ($filterConditionType == 'in') {
                                if (!in_array($itemData, $acceptedValues)) {
                                    $collectionItemValidated = false;
                                    break 3;
                                }
                            }
                            // Date filters not implemented (yet?)
                            #var_dump($filterField, $itemData, $acceptedValues);
                        }
                    }
                }
            }
            // "Export only new" filter: For collections, this is joined in the Xtento_OrderExport_Model_Export model with the exported entity collection directly. This doesn't work for direct model exports. Thus, we need to add the filter here, too.
            if ($this->_exportOnlyNewFilter) {
                $historyCollection = Mage::getModel('xtento_orderexport/history')->getCollection();
                $historyCollection->addFieldToFilter('entity_id', $forcedCollectionItem->getData('entity_id'));
                $historyCollection->addFieldToFilter('entity', $this->getProfile()->getEntity());
                $historyCollection->addFieldToFilter('profile_id', $this->getProfile()->getId());
                if ($historyCollection->count() > 0) {
                    $collectionItemValidated = false;
                }
            }
            #Zend_Debug::dump($forcedCollectionItem->getData());
            #var_dump($collectionItemValidated);
            #die();
            // If all filters pass, then export the item
            if ($this->getExportType() == Xtento_OrderExport_Model_Export::EXPORT_TYPE_TEST || ($collectionItemValidated && $validationProfile->validate($forcedCollectionItem))) {
                $returnData = $this->_exportData(new Xtento_OrderExport_Model_Export_Entity_Collection_Item($forcedCollectionItem, $this->_entityType, 1, 1), $exportFields);
                if (!empty($returnData)) {
                    $this->_returnArray[] = $returnData;
                }
            }
        }
        #var_dump($this->_returnArray); die();
        return $this->_returnArray;
    }

    public function setCollectionFilters($filters)
    {
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                foreach ($filter as $attribute => $filterArray) {
                    $this->_collection->addAttributeToFilter($attribute, $filterArray);
                }
            }
        }
        $this->setRawCollectionFilters($filters);
        return $this->_collection;
    }

    public function addExportOnlyNewFilter()
    {
        $this->_exportOnlyNewFilter = true;
    }

    protected function _exportData($collectionItem, $exportFields)
    {
        return Mage::getSingleton('xtento_orderexport/export_data')
            ->setShowEmptyFields($this->getShowEmptyFields())
            ->setProfile($this->getProfile() ? $this->getProfile() : new Varien_Object())
            ->setExportFields($exportFields)
            ->getExportData($this->_entityType, $collectionItem);
    }

    public function runExport($forcedCollectionItem = false)
    {
        return $this->_runExport($forcedCollectionItem);
    }
}