<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Zblocks
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Zblocks_Block_Adminhtml_Zblocks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('zblocksGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('zblocks_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('zblocks/zblocks')->getCollection();
        $collection->getSelect()
            ->from('', array(   'block_count' => 'count(c.zblock_id)',
                                'position' => "if(block_position='custom', CONCAT(block_position_custom, ' *'), block_position)"))
            ->joinLeft(array('c' => $collection->getTable('zblocks/content')), 'main_table.zblock_id = c.zblock_id', '')
            ->group('main_table.zblock_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
      $this->addColumn('block_title', array(
            'header'    => Mage::helper('zblocks')->__('Title'),
            'align'     => 'left',
            'index'     => 'block_title',
      ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('zblocks')->__('Position'),
            'align'     => 'left',
            'index'     => 'position',
            'filter_condition_callback' => array($this, '_filterPosition'),
        ));

        $this->addColumn('block_count', array(
            'header'    => Mage::helper('zblocks')->__('Blocks'),
            'index'     => 'block_count',
            'align'     => 'right',
            'width'     => '50px',
            'filter_condition_callback' => array($this, '_filterBlockCount'),
        ));

        $this->addColumn('rotator_mode', array(
            'header'    => Mage::helper('zblocks')->__('Mode'),
            'index'     => 'rotator_mode',
            'type'      => 'options',
            'align'     => 'left',
            'width'     => '80px',
            'options'   => Mage::helper('zblocks')->getRotatorModesToOptionsArray(),
        ));

        $this->addColumn('schedule_from_date', array(
            'header'    => Mage::helper('zblocks')->__('Date From'),
            'index'     => 'schedule_from_date',
            'type'      => 'date',
            'width'     => '100px',
        ));

        $this->addColumn('schedule_to_date', array(
            'header'    => Mage::helper('zblocks')->__('Date To'),
            'index'     => 'schedule_to_date',
            'type'      => 'date',
            'width'     => '100px',
        ));

      $this->addColumn('schedule_pattern', array(
            'header'    => Mage::helper('zblocks')->__('Schedule pattern'),
            'index'     => 'schedule_pattern', 
            'type'      => 'options',
            'align'     => 'left',
            'options'   => Mage::helper('zblocks')->getPatternsToOptionsArray(),
      ));

      // $this->addColumn('schedule_from_time', array(
            // 'header'    => Mage::helper('zblocks')->__('From Time'),
            // 'index'     => 'schedule_from_time',
            // 'align'     => 'left',
            // 'width'     => '80px',
      // ));

      // $this->addColumn('schedule_to_time', array(
            // 'header'    => Mage::helper('zblocks')->__('To Time'),
            // 'index'     => 'schedule_to_time',
            // 'align'     => 'left',
            // 'width'     => '80px',
      // ));
/*
        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('zblocks')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
            'width'     => '100px',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('zblocks')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
            'width'     => '100px',
        ));
//*/
        if (!Mage::app()->isSingleStoreMode())
        {
            $this->addColumn('store_ids', array(
                'header'        => Mage::helper('zblocks')->__('Store View'),
                'index'         => 'store_ids',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
               'filter_condition_callback' => array($this, '_filterStoreCondition'),
          ));
      }

        $this->addColumn('block_is_active', array(
            'header'    => Mage::helper('zblocks')->__('Status'),
            'index'     => 'block_is_active',
            'align'     => 'left',
            'width'     => '80px',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('zblocks')->__('Disabled'),
                1 => Mage::helper('zblocks')->__('Enabled')
            ),
        ));

      $this->addColumn('block_sort_order', array(
            'header'    => Mage::helper('zblocks')->__('Sort Order'),
            'align'     => 'right',
            'index'     => 'block_sort_order',
            'width'     => '80px',
      ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('zblocks')->__('Action'),
                'width'     => '80',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('zblocks')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id',
                        'confirm'   => Mage::helper('zblocks')->__('Are you sure you want to delete the block?'),
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

    return parent::_prepareColumns();
  }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) return;
        $collection->getSelect()->where('find_in_set(?, store_ids)', $value);
    }

    protected function _filterPosition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) return;
        $collection->getSelect()->having("position LIKE '%".$value."%'");
    }

    protected function _filterBlockCount($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) return;
        $collection->getSelect()->having('block_count=?', $value);
    }
}
