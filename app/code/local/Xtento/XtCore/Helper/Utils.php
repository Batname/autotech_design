<?php

/**
 * Product:       Xtento_XtCore (1.0.0)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-09-13T12:14:30+02:00
 * File:          app/code/local/Xtento/XtCore/Helper/Utils.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_XtCore_Helper_Utils extends Mage_Core_Helper_Abstract
{
    protected $_modules = false;

    protected $_versionCorrelationEE_CE = array(
        '1.9.1.0' => '1.4.2.0',
        '1.9.1.1' => '1.4.2.0',
        '1.10.0.1' => '1.5.0.1',
        '1.10.1.0' => '1.5.1.0',
        '1.10.1.1' => '1.5.1.0',
        '1.11.0.0' => '1.6.0.0',
        '1.11.0.2' => '1.6.0.0',
        '1.11.1.0' => '1.6.1.0',
        '1.11.2.0' => '1.6.1.0',
        '1.12.0.0' => '1.7.0.0',
        '1.12.0.1' => '1.7.0.0',
        '1.12.0.2' => '1.7.0.0',
    );

    protected $_versionCorrelationPE_CE = array(
        '1.9.1.0' => '1.4.2.0',
        '1.9.1.1' => '1.4.2.0',
        '1.10.0.1' => '1.5.0.1',
        '1.10.1.0' => '1.5.1.0',
        '1.11.0.0' => '1.6.0.0',
        '1.11.1.0' => '1.6.1.0',
    );

    /* Thanks for the inspiration to Sortal. */
    public function mageVersionCompare($version1, $version2, $operator)
    {
        // Detect edition by included modules
        if (!$this->_modules) {
            $this->_modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        }

        $version1 = preg_replace("/[^0-9\.]/", "", $version1);

        if (in_array('Enterprise_CatalogPermissions', $this->_modules)) {
            // Detected enterprise edition
            if (!isset($this->_versionCorrelationEE_CE[$version1])) {
                return version_compare($version1, $version2, $operator);
            } else {
                return version_compare($this->_versionCorrelationEE_CE[$version1], $version2, $operator);
            }
        } elseif (in_array('Enterprise_Enterprise', $this->_modules)) {
            // Detected professional edition
            if (!isset($this->_versionCorrelationPE_CE[$version1])) {
                return version_compare($version1, $version2, $operator);
            } else {
                return version_compare($this->_versionCorrelationPE_CE[$version1], $version2, $operator);
            }
        } else {
            // Detected community edition
            return version_compare($version1, $version2, $operator);
        }
    }

    // Check if a third party extension is installed and enabled
    public function isExtensionInstalled($extensionIdentifier)
    {
        if (!$this->_modules) {
            $this->_modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        }
        if (in_array($extensionIdentifier, $this->_modules)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Is the module running inside professional or enterprise edition?
     */
    public function getIsPEorEE()
    {
        // Detect edition by included modules
        if (!$this->_modules) {
            $this->_modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        }

        if (in_array('Enterprise_CatalogPermissions', $this->_modules)) {
            // Detected enterprise edition
            return true;
        } elseif (in_array('Enterprise_Enterprise', $this->_modules)) {
            // Detected professional edition
            return true;
        } else {
            // Detected community edition
            return false;
        }
    }

    public function isCronRunning() {
        return Mage::getModel('xtcore/observer_cron')->checkCronjob();
    }

    public function getLastCronExecution() {
        return Mage::getModel('xtcore/observer_cron')->getLastExecution();
    }
}