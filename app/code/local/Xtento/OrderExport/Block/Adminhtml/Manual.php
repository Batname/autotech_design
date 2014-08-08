<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-03-05T14:12:58+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Manual.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Manual extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('xtento/orderexport/manual_export.phtml');
    }

    public function getProfileSelectorHtml()
    {
        $html = '<select class="select" name="profile_id" id="profile_id" style="width: 320px;">';
        $html .= '<option value="">' . Mage::helper('xtento_orderexport')->__('--- Select Profile---') . '</option>';
        $enabledProfiles = Mage::getModel('xtento_orderexport/system_config_source_export_profile')->toOptionArray();
        $profilesByGroup = array();
        foreach ($enabledProfiles as $profile) {
            $profilesByGroup[$profile['entity']][] = $profile;
        }
        foreach ($profilesByGroup as $entity => $profiles) {
            $html .= '<optgroup label="' . Mage::helper('xtento_orderexport')->__(ucfirst($entity) . ' Export') . '">';
            foreach ($profiles as $profile) {
                $html .= '<option value="' . $profile['value'] . '" entity="' . $entity . '">' . $profile['label'] . ' (' . Mage::helper('xtento_orderexport')->__('ID: %d', $profile['value']) . ')</option>';
            }
            $html .= '</optgroup>';
        }
        $html .= '</select>';
        return $html;
    }

    public function getStoreViewSelectorHtml()
    {
        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */
        $websiteCollection = $storeModel->getWebsiteCollection();
        $groupCollection = $storeModel->getGroupCollection();
        $storeCollection = $storeModel->getStoreCollection();

        $html = '<select multiple="multiple" id="store_id" name="store_id[]" style="width: 320px; height: 130px; margin-bottom: 10px;">';

        $html .= '<option value="0" selected="selected">' . Mage::helper('adminhtml')->__('All Store Views') . '</option>';

        foreach ($websiteCollection as $website) {
            $websiteShow = false;
            foreach ($groupCollection as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($storeCollection as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $websiteShow = true;
                        $html .= '<optgroup label="' . $website->getName() . '"></optgroup>';
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $html .= '<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;' . $group->getName() . '">';
                    }
                    $html .= '<option value="' . $store->getId() . '">&nbsp;&nbsp;&nbsp;&nbsp;' . $store->getName() . '</option>';
                }
                if ($groupShow) {
                    $html .= '</optgroup>';
                }
            }
        }
        $html .= '</select>';
        return $html;
    }

    public function getCalendarHtml($id)
    {
        $calendar = $this->getLayout()
            ->createBlock('core/html_date')
            ->setId($id)
            ->setName($id)
            ->setClass('input-text')
            ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
            ->setFormat(Varien_Date::DATE_INTERNAL_FORMAT);
        return $calendar->getHtml();
    }

    public function getSelectValues()
    {
        $html = '';
        $statusValues = array();
        foreach (Mage::getModel('xtento_orderexport/export')->getEntities() as $entity => $label) {
            foreach (Mage::getSingleton('xtento_orderexport/system_config_source_export_status')->toOptionArray($entity) as $status) {
                $statusValues[$entity][$status['value']] = $status['label'];
            }
        }
        $html .= $this->arrayToJsHash('status_values', $statusValues);

        $lastIncrementIds = array();
        foreach (Mage::getModel('xtento_orderexport/export')->getEntities() as $entity => $label) {
            $lastIncrementIds[$entity] = Mage::helper('xtento_orderexport/export')->getLastIncrementId($entity);
        }
        $html .= $this->arrayToJsHash('last_increment_ids', $lastIncrementIds);

        $lastExportedIds = array();
        $profileLinks = array();
        foreach (Mage::getModel('xtento_orderexport/system_config_source_export_profile')->toOptionArray(false, false, true) as $profile) {
            $lastExportedIds[$profile['value']] = $profile['last_exported_increment_id'];
            $profileLinks[$profile['value']] = Mage::helper('adminhtml')->getUrl('*/orderexport_profile/edit', array('id' => $profile['value']));
        }
        $html .= $this->arrayToJsHash('last_exported_increment_ids', $lastExportedIds);
        $html .= $this->arrayToJsHash('profile_edit_links', $profileLinks);

        $profileSettings = array();
        $settingsToFetch = array('export_filter_datefrom', 'export_filter_dateto', 'export_filter_status', 'export_filter_new_only', 'export_action_change_status', 'store_ids', 'start_download_manual_export', 'export_filter_last_x_days');
        foreach (Mage::getModel('xtento_orderexport/system_config_source_export_profile')->toOptionArray(false, false, true) as $profile) {
            foreach ($settingsToFetch as $setting) {
                $value = $profile['profile']->getData($setting);
                $profileSettings[$profile['value']][$setting] = $value;
            }
        }
        $html .= $this->arrayToJsHash('profile_settings', $profileSettings);

        return $html;
    }

    public function arrayToJsHash($name, $array)
    {
        $html = 'var ' . $name . ' = $H({' . "\n";
        $loopLength = 0;
        foreach ($array as $index => $data) {
            if (!empty($data) && is_array($data)) {
                $loopLength++;
            }
        }
        $loopCounter = 0;
        foreach ($array as $index => $data) {
            $loopCounter++;
            $loopLength2 = count($array[$index]);
            $loopCounter2 = 0;
            if (is_array($data)) {
                $html .= '\'' . $this->_escapeStringJs($index) . '\': {' . "\n";
                foreach ($data as $code => $label) {
                    $loopCounter2++;
                    $html .= '\'' . $this->_escapeStringJs($code) . '\': \'' . $this->_escapeStringJs($label) . '\'';
                    if ($loopCounter2 !== $loopLength2) {
                        $html .= ',';
                    }
                    $html .= "\n";
                }
                $html .= '}';
                if ($loopCounter !== $loopLength) {
                    $html .= ",\n";
                }
            } else {
                $html .= '\'' . $this->_escapeStringJs($index) . '\': ';
                $html .= '\'' . $this->_escapeStringJs($data) . '\'';
                if ($loopCounter !== count($array)) {
                    $html .= ",\n";
                }
            }
        }
        $html .= "});\n";
        return $html;
    }

    private function _escapeStringJs($string)
    {
        return str_replace("'", "\\'", $string);
    }

    protected function _toHtml()
    {
        $messagesBlock = <<<EOT
<div id="messages_export">
    <ul class="messages">
        <li class="warning-msg" id="warning-msg" style="display:none">
            <ul>
                <li>
                    <span id="warning-msg-text"></span>
                </li>
            </ul>
        </li>
        <li class="success-msg" id="success-msg" style="display:none">
            <ul>
                <li>
                    <span id="success-msg-text"></span>
                </li>
            </ul>
        </li>
    </ul>
</div>
EOT;
        return $messagesBlock . $this->getLayout()->createBlock('xtento_orderexport/adminhtml_widget_menu')->toHtml() . parent::_toHtml();
    }
}