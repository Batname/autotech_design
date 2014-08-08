<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-12-08T12:40:25+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Profile/Edit/Tab/Destination.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Profile_Edit_Tab_Destination extends Xtento_OrderExport_Block_Adminhtml_Destination_Grid
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_orderexport')->__('Export destinations are local directories, FTP/SFTP/HTTP servers, E-Mail recipients or webservices where exported files are saved. If you just want to export manually or from the "Sales" grids, no export destination must be set up. Please click <a href="'.Mage::helper('adminhtml')->getUrl('*/orderexport_destination').'" target="_blank">here</a> to add new export destinations.'));
        return $formMessages;
    }

    protected function _getProfile()
    {
        return Mage::getModel('xtento_orderexport/profile')->load($this->getRequest()->getParam('id'));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('col_destinations', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'col_destinations',
            'values' => $this->getSelectedDestinations(),
            'align' => 'center',
            'index' => 'destination_id'
        ));

        parent::_prepareColumns();
        unset($this->_columns['action']);
        foreach ($this->_columns as $key => $column) {
            if ($key == 'destination_id' || $key == 'col_destinations') {
                continue;
            }
            // Rename column IDs so they're not posted to the profile information
            $column->setId('col_'.$column->getId());
            $this->_columns['col_'.$key] = $column;
            unset($this->_columns[$key]);
        }

        $this->addColumn('action',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xtento_orderexport')->__('Edit Destination'),
                        'url' => array('base' => '*/orderexport_destination/edit'),
                        'field' => 'id',
                        'target' => '_blank'
                    )
                ),
                'filter' => false,
                'sortable' => false,
            )
        );
    }

    protected function _prepareMassaction()
    {
    }

    public function getRowUrl($row)
    {
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/destinationGrid', array('_current' => true));
    }

    public function getSelectedDestinations()
    {
        $array = explode("&", $this->_getProfile()->getDestinationIds());
        return $array;
    }

    protected function _toHtml()
    {
        if ($this->getRequest()->getParam('ajax')) {
            return parent::_toHtml();
        }
        return $this->_getFormMessages() . parent::_toHtml();
    }

    protected function _getFormMessages()
    {
        $html = '<div id="messages"><ul class="messages">';
        foreach ($this->getFormMessages() as $formMessage) {
            $html .= '<li class="' . $formMessage['type'] . '-msg"><ul><li><span>' . $formMessage['message'] . '</span></li></ul></li>';
        }
        $html .= '</ul></div>';
        return $html;
    }
}