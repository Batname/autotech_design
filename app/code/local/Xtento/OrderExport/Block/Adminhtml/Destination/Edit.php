<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-02-24T21:54:08+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Destination/Edit.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Destination_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'xtento_orderexport';
        $this->_controller = 'adminhtml_destination';

        if (Mage::registry('destination')->getId()) {
            $this->_updateButton('save', 'label', Mage::helper('xtento_orderexport')->__('Save Destination'));
            $this->_removeButton('delete');
            $this->_addButton('delete', array(
                'label' => Mage::helper('adminhtml')->__('Delete Destination'),
                'class' => 'delete',
                'onclick' => 'deleteConfirm(\'' . Mage::helper('xtento_orderexport')->__('Are you sure you want to do this? This destination is in use by %d profiles.', (Mage::registry('destination')) ? count(Mage::registry('destination')->getProfileUsage()) : 0)
                    . '\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('xtento_orderexport')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = <<<EOT
            function saveAndContinueEdit() {
                if (editForm && editForm.validator.validate()) {
                    var tabsIdValue = destination_tabsJsTabs.activeTab.id;
                    var tabsBlockPrefix = 'destination_tabs_';
                    if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                        tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                    }
                }
                editForm.submit($('edit_form').action+'continue/edit/active_tab/'+tabsIdValue);
            }
EOT;
        if (Mage::registry('destination') && Mage::registry('destination')->getId()) {
            $this->_formScripts[] = <<<EOT
            varienGlobalEvents.attachEventHandler("formSubmit", function(){
                if (editForm && editForm.validator.validate()) {
                    Element.show('loading-mask');
                    setLoaderPosition();
                    $('loading_mask_loader').setStyle({width: 'auto'});
                    $('loading_mask_loader').innerHTML = $('loading_mask_loader').innerHTML + '<br/><br/>' + '{$this->__('The connection is being tested...')}';
                }
            });
EOT;
        }

        if (!Mage::registry('destination') || !Mage::registry('destination')->getId()) {
            $this->_removeButton('save');
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('destination')->getId()) {
            return Mage::helper('xtento_orderexport')->__('Edit Destination \'%s\'', Mage::helper('xtcore/core')->escapeHtml(Mage::registry('destination')->getName()));
        } else {
            return Mage::helper('xtento_orderexport')->__('New Destination');
        }
    }

    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('xtento_orderexport/adminhtml_widget_menu')->setShowWarning(1)->toHtml() . parent::_toHtml();
    }
}