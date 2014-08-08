<?php
/**
* Ext4mage Orders2csv Module
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to Henrik Kier <info@ext4mage.com> so we can send you a copy immediately.
*
* @category   Ext4mage
* @package    Ext4mage_Orders2csv
* @copyright  Copyright (c) 2012 Ext4mage (http://ext4mage.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @author     Henrik Kier <info@ext4mage.com>
* */
class Ext4mage_Orders2csv_Block_Adminhtml_File_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('fileGrid');
		$this->setDefaultSort('file_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('orders2csv/file')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('file_id', array(
          'header'    => Mage::helper('orders2csv')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'file_id',
		));

		$this->addColumn('title', array(
          'header'    => Mage::helper('orders2csv')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
		));

		$this->addColumn('creation_time', array(
			'header'    => Mage::helper('orders2csv')->__('Created at'),
			'align'     => 'left',
			'width'     => '120px',
			'type'      => 'date',
			'default'   => '--',
			'index'     => 'creation_time',
		));

		$this->addColumn('update_time', array(
			'header'    => Mage::helper('orders2csv')->__('Updated at'),
			'align'     => 'left',
			'width'     => '120px',
			'type'      => 'date',
			'default'   => '--',
			'index'     => 'update_time',
		));

		$this->addColumn('is_active', array(
          'header'    => Mage::helper('orders2csv')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'is_active',
          'type'      => 'options',
          'options'   => array(
		1 => 'Enabled',
		2 => 'Disabled',
		),
		));
		 
		$this->addColumn('action',
		array(
                'header'    =>  Mage::helper('orders2csv')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
		array(
                        'caption'   => Mage::helper('orders2csv')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
		)
		),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('file_id');
		$this->getMassactionBlock()->setFormFieldName('file');

		$this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('orders2csv')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('orders2csv')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('orders2csv/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('orders2csv')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('orders2csv')->__('Status'),
                         'values' => $statuses
		)
		)
		));
		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}