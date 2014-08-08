<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-02-10T13:10:41+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Destination.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Destination extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'xtento_orderexport';
        $this->_controller = 'adminhtml_destination';
        $this->_headerText = Mage::helper('xtento_orderexport')->__('Sales Export - Destinations');
        $this->_addButtonLabel = Mage::helper('xtento_orderexport')->__('Add New Destination');
        parent::__construct();
    }

    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('xtento_orderexport/adminhtml_widget_menu')->toHtml() . parent::_toHtml();
    }
}