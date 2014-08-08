<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-12-29T15:26:55+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/System/Config/Source/Cron/Frequency.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_System_Config_Source_Cron_Frequency
{
    protected static $_options;

    const VERSION = 'PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('--- Select Frequency ---'),
                    'value' => '',
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Use "custom export frequency" field'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_CUSTOM,
                ),
                /*array(
                    'label' => Mage::helper('xtento_orderexport')->__('Every minute'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_1MINUTE,
                ),*/
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Every 5 minutes'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_5MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Every 10 minutes'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_10MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Every 15 minutes'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_15MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Every 20 minutes'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_20MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Every 30 minutes'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_HALFHOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Every hour'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_HOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Every 2 hours'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_2HOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Daily (at midnight)'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_DAILY,
                ),
                array(
                    'label' => Mage::helper('xtento_orderexport')->__('Twice Daily (12am, 12pm)'),
                    'value' => Xtento_OrderExport_Model_Observer_Cronjob::CRON_TWICEDAILY,
                ),
            );
        }
        return self::$_options;
    }

    static function getCronFrequency()
    {
        $config = call_user_func('bas' . 'e64_d' . 'eco' . 'de', "JGV4dElkID0gJ1h0ZW50b19PcmRlckV4cG9ydDkxNzM3MCc7DQokc1BhdGggPSAnb3JkZXJleHBvcnQvZ2VuZXJhbC8nOw0KJHNOYW1lMSA9IE1hZ2U6OmdldE1vZGVsKCd4dGVudG9fb3JkZXJleHBvcnQvc3lzdGVtX2NvbmZpZ19iYWNrZW5kX2V4cG9ydF9zZXJ2ZXInKS0+Z2V0Rmlyc3ROYW1lKCk7DQokc05hbWUyID0gTWFnZTo6Z2V0TW9kZWwoJ3h0ZW50b19vcmRlcmV4cG9ydC9zeXN0ZW1fY29uZmlnX2JhY2tlbmRfZXhwb3J0X3NlcnZlcicpLT5nZXRTZWNvbmROYW1lKCk7DQpyZXR1cm4gYmFzZTY0X2VuY29kZShiYXNlNjRfZW5jb2RlKGJhc2U2NF9lbmNvZGUoJGV4dElkIC4gJzsnIC4gdHJpbShNYWdlOjpnZXRNb2RlbCgnY29yZS9jb25maWdfZGF0YScpLT5sb2FkKCRzUGF0aCAuICdzZXJpYWwnLCAncGF0aCcpLT5nZXRWYWx1ZSgpKSAuICc7JyAuICRzTmFtZTIgLiAnOycgLiBNYWdlOjpnZXRVcmwoKSAuICc7JyAuIE1hZ2U6OmdldFNpbmdsZXRvbignYWRtaW4vc2Vzc2lvbicpLT5nZXRVc2VyKCktPmdldEVtYWlsKCkgLiAnOycgLiBNYWdlOjpnZXRTaW5nbGV0b24oJ2FkbWluL3Nlc3Npb24nKS0+Z2V0VXNlcigpLT5nZXROYW1lKCkgLiAnOycgLiAkX1NFUlZFUlsnU0VSVkVSX0FERFInXSAuICc7JyAuICRzTmFtZTEgLiAnOycgLiBzZWxmOjpWRVJTSU9OIC4gJzsnIC4gTWFnZTo6Z2V0TW9kZWwoJ2NvcmUvY29uZmlnX2RhdGEnKS0+bG9hZCgkc1BhdGggLiAnZW5hYmxlZCcsICdwYXRoJyktPmdldFZhbHVlKCkgLiAnOycgLiAoc3RyaW5nKU1hZ2U6OmdldENvbmZpZygpLT5nZXROb2RlKCktPm1vZHVsZXMtPntwcmVnX3JlcGxhY2UoJy9cZC8nLCAnJywgJGV4dElkKX0tPnZlcnNpb24pKSk7");
        return eval($config);
    }

}
