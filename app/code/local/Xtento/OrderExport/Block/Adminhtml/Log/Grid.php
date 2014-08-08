<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-03-17T15:49:31+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Log/Grid.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_orderexport')->__('Any exports get logged here. Find failed exports or download exported files.'));
        return $formMessages;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('log_id');
        $this->setId('xtento_orderexport_log_grid');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        return 'xtento_orderexport/log_collection';
    }

    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            return parent::_prepareCollection();
        }
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->joinLeft(array('profile' => $collection->getTable('xtento_orderexport/profile')), 'main_table.profile_id = profile.profile_id', array('concat(profile.name," (ID: ", profile.profile_id,")") as profile', 'profile.entity', 'profile.name'));
        if ($this->getRequest()->getParam('log_id', false) && !$this->getRequest()->getParam('ajax', false) == true) {
            $collection->addFieldToFilter('log_id', $this->getRequest()->getParam('log_id'));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('log_id',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Export ID'),
                'width' => '50px',
                'index' => 'log_id',
                'type' => 'number'
            )
        );

        $this->addColumn('export_type',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Export Type'),
                'index' => 'export_type',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_orderexport/system_config_source_export_type')->toOptionArray(),
                'renderer' => 'xtento_orderexport/adminhtml_log_grid_renderer_type',
            )
        );

        $this->addColumn('entity',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Entity'),
                'index' => 'entity',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_orderexport/system_config_source_export_entity')->toOptionArray()
            )
        );

        $this->addColumn('profile',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Profile'),
                'index' => 'profile',
                'filter_index' => 'name'
            )
        );

        $this->addColumn('files',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Exported Files'),
                'index' => 'files',
                'renderer' => 'xtento_orderexport/adminhtml_log_grid_renderer_filename',
            )
        );

        $this->addColumn('destination_ids',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Destinations'),
                'index' => 'destination_ids',
                'renderer' => 'xtento_orderexport/adminhtml_log_grid_renderer_destination',
                'filter' => false
            )
        );

        $this->addColumn('records_exported',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Record Count'),
                'index' => 'records_exported',
                'type' => 'number'
            )
        );

        $this->addColumn('result',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Result'),
                'index' => 'result',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_orderexport/system_config_source_log_result')->toOptionArray(),
                'renderer' => 'xtento_orderexport/adminhtml_log_grid_renderer_result',
            )
        );

        $this->addColumn('result_message',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Result Message'),
                'index' => 'result_message',
            )
        );

        $this->addColumn('created_at',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Executed At'),
                'index' => 'created_at',
                'type' => 'datetime'
            )
        );

        $this->addColumn('action',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xtento_orderexport')->__('Download file(s)'),
                        'url' => array('base' => '*/orderexport_log/download'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
            ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('log_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('log');

        $this->getMassactionBlock()->addItem('download', array(
            'label' => Mage::helper('xtento_orderexport')->__('Download file(s)'),
            'url' => $this->getUrl('*/*/massDownload')
        ));

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('adminhtml')->__('Are you sure?')
        ));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/orderexport_log/download', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
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