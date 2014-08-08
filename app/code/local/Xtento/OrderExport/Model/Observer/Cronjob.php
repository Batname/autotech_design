<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-05-24T12:35:59+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Observer/Cronjob.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Observer_Cronjob extends Xtento_OrderExport_Model_Observer_Abstract
{
    const CRON_CUSTOM = 'custom';
    const CRON_1MINUTE = '* * * * *';
    const CRON_5MINUTES = '*/5 * * * *';
    const CRON_10MINUTES = '*/10 * * * *';
    const CRON_15MINUTES = '*/15 * * * *';
    const CRON_20MINUTES = '*/20 * * * *';
    const CRON_HALFHOURLY = '*/30 * * * *';
    const CRON_HOURLY = '0 * * * *';
    const CRON_2HOURLY = '0 */2 * * *';
    const CRON_DAILY = '0 0 * * *';
    const CRON_TWICEDAILY = '0 0,12 * * *';

    public function export($schedule)
    {
        try {
            if (!Mage::helper('xtento_orderexport')->getModuleEnabled() || !Mage::helper('xtento_orderexport')->isModuleProperlyInstalled()) {
                return;
            }
            if (!$schedule) {
                return;
            }
            $jobCode = $schedule->getJobCode();
            preg_match('/xtento_orderexport_profile_(\d+)/', $jobCode, $jobMatch);
            if (!isset($jobMatch[1])) {
                Mage::throwException(Mage::helper('xtento_orderexport/export')->__('No profile ID found in job_code.'));
            }
            $profileId = $jobMatch[1];
            $profile = Mage::getModel('xtento_orderexport/profile')->load($profileId);
            if (!$profile->getId()) {
                Mage::throwException(Mage::helper('xtento_orderexport/export')->__('Profile ID %d does not seem to exist anymore.', $profileId));
            }
            if (!$profile->getEnabled()) {
                return; // Profile not enabled
            }
            if (!$profile->getCronjobEnabled()) {
                return; // Cronjob not enabled
            }
            $exportModel = Mage::getModel('xtento_orderexport/export', array('profile' => $profile));
            $filters = $this->addProfileFilters($profile);
            $exportModel->cronExport($filters);
        } catch (Exception $e) {
            Mage::log('Cronjob exception for job_code ' . $jobCode . ': ' . $e->getMessage(), null, 'xtento_orderexport_cron.log', true);
            return;
        }
    }

    public function addProfileFilters($profile) {
        return $this->_addProfileFilters($profile);
    }
}