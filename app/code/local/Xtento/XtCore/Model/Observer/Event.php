<?php

/**
 * Product:       Xtento_XtCore (1.0.0)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-10-30T18:29:25+01:00
 * File:          app/code/local/Xtento/XtCore/Model/Observer/Event.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_XtCore_Model_Observer_Event
{
    protected static $hasRun;

    public function cronExecution()
    {
        if (!self::$hasRun) {
            self::$hasRun = 1;
        } else {
            return $this;
        }

        // Called by event, save last cron execution for XTENTO modules
        // Mage::getConfig()->saveConfig('xtcore/crontest/last_execution', time(), 'default', 0)->reinit();
        Mage::getResourceModel('xtcore/config')->saveConfig('xtcore/crontest/last_execution', time());

        if (Mage::helper('xtcore/utils')->isExtensionInstalled('TBT_Testsweet')) {
            // Save last cron execution for TBT_Testweet
            Mage::getConfig()->saveConfig('testsweet/crontest/timestamp', time(), 'default', 0)->reinit();
        }

        return $this;
    }
}