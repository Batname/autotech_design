<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-12-17T14:11:01+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Destination/Grid/Renderer/Result.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Destination_Grid_Renderer_Result extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getLastResult() === NULL) {
            return '<span class="grid-severity-major"><span>' . Mage::helper('xtento_orderexport')->__('No Result') . '</span></span>';
        } else if ($row->getLastResult() == 0) {
            return '<span class="grid-severity-critical"><span>' . Mage::helper('xtento_orderexport')->__('Failed') . '</span></span>';
        } else if ($row->getLastResult() == 1) {
            return '<span class="grid-severity-notice"><span>' . Mage::helper('xtento_orderexport')->__('Success') . '</span></span>';
        }
    }
}