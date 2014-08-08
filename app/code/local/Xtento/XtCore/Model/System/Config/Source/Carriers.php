<?php

/**
 * Product:       Xtento_XtCore (1.0.0)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-07-17T23:15:30+02:00
 * File:          app/code/local/Xtento/XtCore/Model/System/Config/Source/Carriers.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_XtCore_Model_System_Config_Source_Carriers
{
    public function toOptionArray()
    {
        $carriers = array();
        $carriers[] = array('value' => 'custom', 'label' => Mage::helper('adminhtml')->__('Custom Carrier'));
        try {
            foreach (Mage::getSingleton('shipping/config')->getAllCarriers() as $carrierCode => $carrierConfig) {
                if ($carrierConfig->isTrackingAvailable()) {
                    $carriers[] = array('value' => $carrierCode, 'label' => $this->_determineCarrierTitle($carrierCode));
                }
            }
        } catch (Mage_Shipping_Exception $e) {
            // Do nothing and damn those not correctly removed shipping methods.
        }
        return $carriers;
    }

    private function _determineCarrierTitle($carrierCode)
    {
        if (!isset($this->_carriers[$carrierCode])) {
            if ($carrierCode == 'custom') {
                $this->_carriers[$carrierCode] = 'Custom';
            } else {
                $this->_carriers[$carrierCode] = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
                if (empty($this->_carriers[$carrierCode])) {
                    // Try to get carrier from XTENTO custom carrier trackers extension
                    $this->_carriers[$carrierCode] = Mage::getStoreConfig('customtrackers/' . $carrierCode . '/title');
                }
            }
        }
        return $this->_carriers[$carrierCode];
    }

}
