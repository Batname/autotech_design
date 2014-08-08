<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-10-25T14:29:30+02:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Profile/Edit/Tabs.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('profile_tabs');
        $this->setDestElementId('edit_form');
        if (!Mage::registry('order_export_profile')) {
            $this->setTitle(Mage::helper('xtento_orderexport')->__('Export Profile'));
        } else {
            $this->setTitle(Mage::helper('xtento_orderexport')->__('%s Export Profile', ucfirst(Mage::registry('order_export_profile')->getEntity())));
        }
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label' => Mage::helper('xtento_orderexport')->__('General Configuration'),
            'title' => Mage::helper('xtento_orderexport')->__('General Configuration'),
            'content' => $this->getLayout()->createBlock('xtento_orderexport/adminhtml_profile_edit_tab_general')->toHtml(),
        ));

        if (!Mage::registry('order_export_profile') || !Mage::registry('order_export_profile')->getId()) {
            // We just want to display the "General" tab to set the export type for new profiles
            return parent::_beforeToHtml();
        }

        if (Mage::helper('xtento_orderexport')->getFieldsTabEnabled()) {
            $this->addTab('fields', array(
                'label' => Mage::helper('xtento_orderexport')->__('Export Fields'),
                'title' => Mage::helper('xtento_orderexport')->__('Export Fields'),
                'content' => $this->getLayout()->createBlock('xtento_orderexport/adminhtml_profile_edit_tab_fields')->toHtml(),
            ));
        }

        $this->addTab('destination', array(
            'label' => Mage::helper('xtento_orderexport')->__('Export Destinations'),
            'title' => Mage::helper('xtento_orderexport')->__('Export Destinations'),
            'url' => $this->getUrl('*/*/destination', array('_current' => true)),
            'class' => 'ajax',
        ));

        $this->addTab('output', array(
            'label' => Mage::helper('xtento_orderexport')->__('Output Format'),
            'title' => Mage::helper('xtento_orderexport')->__('Output Format'),
            'content' => $this->getLayout()->createBlock('xtento_orderexport/adminhtml_profile_edit_tab_output')->toHtml(),
        ));

        $this->addTab('conditions', array(
            'label' => Mage::helper('xtento_orderexport')->__('Filters / Actions'),
            'title' => Mage::helper('xtento_orderexport')->__('Filters / Actions'),
            'content' => $this->getLayout()->createBlock('xtento_orderexport/adminhtml_profile_edit_tab_conditions')->toHtml(),
        ));

        $this->addTab('manual', array(
            'label' => Mage::helper('xtento_orderexport')->__('Manual Export'),
            'title' => Mage::helper('xtento_orderexport')->__('Manual Export'),
            'content' => $this->getLayout()->createBlock('xtento_orderexport/adminhtml_profile_edit_tab_manual')->toHtml(),
        ));

        $this->addTab('automatic', array(
            'label' => Mage::helper('xtento_orderexport')->__('Automatic Export'),
            'title' => Mage::helper('xtento_orderexport')->__('Automatic Export'),
            'content' => $this->getLayout()->createBlock('xtento_orderexport/adminhtml_profile_edit_tab_automatic')->toHtml(),
        ));

        $this->addTab('log', array(
            'label' => Mage::helper('xtento_orderexport')->__('Profile Execution Log'),
            'title' => Mage::helper('xtento_orderexport')->__('Profile Execution Log'),
            'content' => $this->getLayout()->createBlock('xtento_orderexport/adminhtml_profile_edit_tab_log')->toHtml(),
        ));

        $this->addTab('history', array(
            'label' => Mage::helper('xtento_orderexport')->__('Profile Export History'),
            'title' => Mage::helper('xtento_orderexport')->__('Profile Export History'),
            'content' => $this->getLayout()->createBlock('xtento_orderexport/adminhtml_profile_edit_tab_history')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}