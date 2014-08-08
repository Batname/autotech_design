<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-11-25T16:07:00+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/System/Config/Source/Log/Result.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_System_Config_Source_Log_Result
{
    public function toOptionArray()
    {
        $values = array();
        $values[Xtento_OrderExport_Model_Log::RESULT_NORESULT] = Mage::helper('xtento_orderexport')->__('No Result');
        $values[Xtento_OrderExport_Model_Log::RESULT_SUCCESSFUL] = Mage::helper('xtento_orderexport')->__('Successful');
        $values[Xtento_OrderExport_Model_Log::RESULT_WARNING] = Mage::helper('xtento_orderexport')->__('Warning');
        $values[Xtento_OrderExport_Model_Log::RESULT_FAILED] = Mage::helper('xtento_orderexport')->__('Failed');
        return $values;
    }
}