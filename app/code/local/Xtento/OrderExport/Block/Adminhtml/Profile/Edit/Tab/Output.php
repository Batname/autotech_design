<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-09-08T16:10:39+02:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Profile/Edit/Tab/Output.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Profile_Edit_Tab_Output extends Xtento_OrderExport_Block_Adminhtml_Widget_Tab
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_orderexport')->__('The XSL Template "translates" the internal Magento database format into your required output format. You can find more information about XSL Templates in our <a href="http://support.xtento.com/wiki/Magento_Extensions:Magento_Order_Export_Module" target="_blank">support wiki</a>. If you don\'t want to create the XSL Template yourself, please refer to our <a href="http://www.xtento.com/magento-services/xsl-template-creation-service.html" target="_blank">XSL Template Creation Service</a>.'));
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $this->setTemplate('xtento/orderexport/output.phtml');
        return parent::_prepareForm();
    }

    protected function getTestIncrementId()
    {
        $profile = Mage::registry('order_export_profile');
        if (!$profile->getEntity()) {
            return '';
        }
        $testId = $profile->getTestId();
        if (!$testId || $testId == 0) {
            return Mage::helper('xtento_orderexport/export')->getLastIncrementId($profile->getEntity());
        } else {
            return $testId;
        }
    }

    protected function getXslTemplate()
    {
        return htmlspecialchars(Mage::registry('order_export_profile')->getXslTemplate(), ENT_NOQUOTES);
    }
}