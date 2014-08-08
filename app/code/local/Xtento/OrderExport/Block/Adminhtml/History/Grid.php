<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-10-17T16:57:21+02:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/History/Grid.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_orderexport')->__("Exported objects get logged here. You can see when an object was exported. Look up the execution log entry to see why. You can also delete objects here and have them re-exported if \"Export only new objects\" is set to \"Yes\"."));
        return $formMessages;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('history_id');
        $this->setId('xtento_orderexport_history_grid');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    protected function _getProfile()
    {
        return Mage::registry('order_export_profile') ? Mage::registry('order_export_profile') : Mage::getModel('xtento_orderexport/profile')->load($this->getRequest()->getParam('id'));
    }

    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            return parent::_prepareCollection();
        }
        $collection = Mage::getResourceModel('xtento_orderexport/history_collection');
        $collection->getSelect()->joinLeft(array('profile' => $collection->getTable('xtento_orderexport/profile')), 'main_table.profile_id = profile.profile_id', array('concat(profile.name," (ID: ", profile.profile_id,")") as profile', 'profile.entity', 'profile.name'));
        $collection->getSelect()->joinLeft(array('order' => $collection->getTable('sales/order')), 'main_table.entity_id = order.entity_id and profile.entity="order"', array('order.increment_id as order_increment_id'));
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.1', '>')) {
            $collection->getSelect()->joinLeft(array('invoice' => $collection->getTable('sales/invoice')), 'main_table.entity_id = invoice.entity_id and profile.entity="invoice"', array('invoice.increment_id as invoice_increment_id'));
            $collection->getSelect()->joinLeft(array('shipment' => $collection->getTable('sales/shipment')), 'main_table.entity_id = shipment.entity_id and profile.entity="shipment"', array('shipment.increment_id as shipment_increment_id'));
            $collection->getSelect()->joinLeft(array('creditmemo' => $collection->getTable('sales/creditmemo')), 'main_table.entity_id = creditmemo.entity_id and profile.entity="creditmemo"', array('creditmemo.increment_id as creditmemo_increment_id'));
        }
        #$collection->getSelect()->columns(new Zend_Db_Expr("CONCAT(order.increment_id, invoice.increment_id, shipment.increment_id, creditmemo.increment_id) AS all_increment_ids"));
        #echo $collection->getSelect(); die();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('history_id',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('History ID'),
                'width' => '50px',
                'index' => 'history_id',
                'type' => 'number'
            )
        );

        $this->addColumn('log_id',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Log / Export ID'),
                'width' => '50px',
                'index' => 'log_id',
                'type' => 'number'
            )
        );

        $this->addColumn('profile',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Profile'),
                'index' => 'profile',
                'filter_index' => 'name'
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

        $this->addColumn('increment_id',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Increment ID'),
                'width' => '50px',
                'index' => 'increment_id',
                'filter_index' => 'entity_id',
                'type' => 'text',
                'renderer' => 'xtento_orderexport/adminhtml_history_grid_renderer_increment',
                'filter_condition_callback' => array($this, '_filterIncrementId'),
            )
        );

        $this->addColumn('entity_id',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Entity ID'),
                'width' => '50px',
                'index' => 'entity_id',
                'type' => 'number',
                'filter_index' => 'main_table.entity_id'
            )
        );

        $this->addColumn('exported_at',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Exported At'),
                'index' => 'exported_at',
                'type' => 'datetime'
            )
        );

        $this->addColumn('view_log_entry',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Log Entry'),
                'type' => 'action',
                'getter' => 'getLogId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xtento_orderexport')->__('View Execution Log Entry'),
                        'url' => array('base' => '*/orderexport_log/'),
                        'field' => 'log_id',
                        'target' => '_blank'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
            )
        );

        $this->addColumn('delete',
            array(
                'header' => Mage::helper('xtento_orderexport')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xtento_orderexport')->__('Delete Entry'),
                        'url' => array('base' => '*/orderexport_history/delete'),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _filterIncrementId($collection, $column)
    {
        if (!$value = trim($column->getFilter()->getValue())) {
            return;
        }

        $value = '%' . $value . '%';

        // addFieldToFilter is not able to handle or conditions from arrays in custom collections
        $sqlArr = array(
            $this->getCollection()->getConnection()->quoteInto("order.increment_id LIKE ?", $value),
            $this->getCollection()->getConnection()->quoteInto("invoice.increment_id LIKE ?", $value),
            $this->getCollection()->getConnection()->quoteInto("shipment.increment_id LIKE ?", $value),
            $this->getCollection()->getConnection()->quoteInto("creditmemo.increment_id LIKE ?", $value),
        );
        $conditionSql = '(' . join(') OR (', $sqlArr) . ')';
        $this->getCollection()->getSelect()->where($conditionSql, null, Varien_Db_Select::TYPE_CONDITION);
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('history_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('history');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete Entries'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('adminhtml')->__('Are you sure? These objects will eventually get re-exported.')
        ));
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