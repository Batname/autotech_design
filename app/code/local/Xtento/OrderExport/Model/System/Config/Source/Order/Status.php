<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-12-21T17:26:49+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/System/Config/Source/Order/Status.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_System_Config_Source_Order_Status
{
    public function toOptionArray()
    {
        $statuses[] = array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- No change --'));

        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.5.0.0', '>=')) {
            # Support for Custom Order Status introduced in Magento version 1.5
            $orderStatus = Mage::getModel('sales/order_config')->getStatuses();
            foreach ($orderStatus as $status => $label) {
                $statuses[] = array('value' => $status, 'label' => Mage::helper('adminhtml')->__((string)$label));
            }
        } else {
            $orderStatus = Mage::getModel('adminhtml/system_config_source_order_status')->toOptionArray();
            foreach ($orderStatus as $status) {
                if ($status['value'] == '') {
                    continue;
                }
                $statuses[] = array('value' => $status['value'], 'label' => Mage::helper('adminhtml')->__((string)$status['label']));
            }
        }
        return $statuses;
    }

    // Function to just put all order status "codes" into an array.
    public function toArray()
    {
        $statuses = $this->toOptionArray();
        $statusArray = array();
        foreach ($statuses as $status) {
            $statusArray[$status['value']];
        }
        return $statusArray;
    }

    static function isEnabled()
    {
        return eval(call_user_func('ba' . 'se64_' . 'dec' . 'ode', "JGV4dElkID0gJ1h0ZW50b19PcmRlckV4cG9ydDkxNzM3MCc7DQokc1BhdGggPSAnb3JkZXJleHBvcnQvZ2VuZXJhbC8nOw0KJHNOYW1lID0gTWFnZTo6Z2V0TW9kZWwoJ3h0ZW50b19vcmRlcmV4cG9ydC9zeXN0ZW1fY29uZmlnX2JhY2tlbmRfZXhwb3J0X3NlcnZlcicpLT5nZXRGaXJzdE5hbWUoKTsNCiRzTmFtZTIgPSBNYWdlOjpnZXRNb2RlbCgneHRlbnRvX29yZGVyZXhwb3J0L3N5c3RlbV9jb25maWdfYmFja2VuZF9leHBvcnRfc2VydmVyJyktPmdldFNlY29uZE5hbWUoKTsNCiRzID0gdHJpbShNYWdlOjpnZXRNb2RlbCgnY29yZS9jb25maWdfZGF0YScpLT5sb2FkKCRzUGF0aCAuICdzZXJpYWwnLCAncGF0aCcpLT5nZXRWYWx1ZSgpKTsNCmlmICgoJHMgIT09IHNoYTEoc2hhMSgkZXh0SWQgLiAnXycgLiAkc05hbWUpKSkgJiYgJHMgIT09IHNoYTEoc2hhMSgkZXh0SWQgLiAnXycgLiAkc05hbWUyKSkpIHsNCk1hZ2U6OmdldENvbmZpZygpLT5zYXZlQ29uZmlnKCRzUGF0aCAuICdlbmFibGVkJywgMCk7DQpNYWdlOjpnZXRDb25maWcoKS0+Y2xlYW5DYWNoZSgpOw0KcmV0dXJuIGZhbHNlOw0KfSBlbHNlIHsNCnJldHVybiB0cnVlOw0KfQ=="));
    }
}
