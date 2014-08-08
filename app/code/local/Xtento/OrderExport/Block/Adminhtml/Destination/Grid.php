<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-12-20T13:06:52+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Destination/Grid.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Destination_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('destination_id');
        $this->setId('xtento_orderexport_destination_grid');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        return 'xtento_orderexport/destination_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('destination_id',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Destination ID'),
                'width' => '50px',
                'index' => 'destination_id',
                'type' => 'number'
            )
        );

        $this->addColumn('type',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Type'),
                'index' => 'type',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_orderexport/system_config_source_destination_type')->toOptionArray(),
            )
        );

        $this->addColumn('name',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('configuration',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Configuration'),
                'index' => 'destination_id',
                'filter' => false,
                'renderer' => 'xtento_orderexport/adminhtml_destination_grid_renderer_configuration',
                'width' => '180px'
            )
        );

        $this->addColumn('status',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Status'),
                'index' => 'destination_id',
                'filter' => false,
                'renderer' => 'xtento_orderexport/adminhtml_destination_grid_renderer_status',
            )
        );

        $this->addColumn('last_result',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Last Result'),
                'index' => 'last_result',
                'type' => 'options',
                'options' => array(
                    0 => Mage::helper('xtento_orderexport')->__('Failed'),
                    1 => Mage::helper('xtento_orderexport')->__('Success'),
                ),
                'renderer' => 'xtento_orderexport/adminhtml_destination_grid_renderer_result',
            )
        );

        $this->addColumn('last_result_message',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Last Result Message'),
                'index' => 'last_result_message',
                'type' => 'text'
            )
        );

        $this->addColumn('last_modification',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Last Modification'),
                'index' => 'last_modification',
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
                        'caption' => Mage::helper('xtento_orderexport')->__('Edit Destination'),
                        'url' => array('base' => '*/orderexport_destination/edit'),
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
        $this->setMassactionIdField('destination_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('destination');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}