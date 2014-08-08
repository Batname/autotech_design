<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-11-09T15:52:32+01:00
 * File:          app/code/local/Xtento/OrderExport/Helper/Data.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Helper_Data extends Mage_Core_Helper_Abstract
{
    static $_isModuleProperlyInstalled = null;
    const EDITION = 'CE';

    public function getDebugEnabled()
    {
        return Mage::getStoreConfigFlag('orderexport/general/debug');
    }

    public function isDebugEnabled()
    {
        return Mage::getStoreConfigFlag('orderexport/general/debug') && ($debug_email = Mage::getStoreConfig('orderexport/general/debug_email')) && !empty($debug_email);
    }

    public function getDebugEmail()
    {
        return Mage::getStoreConfig('orderexport/general/debug_email');
    }

    public function getModuleEnabled()
    {
        if (!Mage::getStoreConfigFlag('orderexport/general/enabled')) {
            return 0;
        }
        $moduleEnabled = Mage::getModel('core/config_data')->load('orderexport/general/' . str_rot13('frevny'), 'path')->getValue();
        if (empty($moduleEnabled) || !$moduleEnabled || (0x28 !== strlen(trim($moduleEnabled)))) {
            return 0;
        }
        if (!Mage::registry('moduleString')) {
            Mage::register('moduleString', 'false');
        }
        return true;
    }

    public function isModuleProperlyInstalled()
    {
        // Check if DB table(s) have been created.
        if (self::$_isModuleProperlyInstalled !== null) {
            return self::$_isModuleProperlyInstalled;
        } else {
            self::$_isModuleProperlyInstalled = (Mage::getSingleton('core/resource')->getConnection('core_read')->showTableStatus(Mage::getSingleton('core/resource')->getTableName('xtento_orderexport_profile')) !== false);
            return self::$_isModuleProperlyInstalled;
        }
    }

    /*
     * Is the "fields" tab to specify fields to export enabled or disabled?
     */
    public function getFieldsTabEnabled()
    {
        $fieldsTabFile = Mage::helper('xtcore/filesystem')->getModuleDir($this) . DS . 'xtento' . DS . 'fields_tab.feature';
        if (@file_exists($fieldsTabFile)) {
            return true;
        }
        return false;
    }

    public function getMsg()
    {
        return Mage::helper('xtento_orderexport')->__(str_rot13('Gur Beqre Rkcbeg Zbqhyr vf abg ranoyrq. Cyrnfr znxr fher lbh\'er hfvat n inyvq yvprafr xrl naq gung gur zbqhyr unf orra ranoyrq ng Flfgrz > KGRAGB Rkgrafvbaf > Fnyrf Rkcbeg pbasvthengvba.'));
    }
}