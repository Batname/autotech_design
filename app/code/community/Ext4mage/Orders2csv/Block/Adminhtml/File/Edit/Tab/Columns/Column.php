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
class Ext4mage_Orders2csv_Block_Adminhtml_File_Edit_Tab_Columns_Column extends Mage_Adminhtml_Block_Widget
{
	protected $_itemCount = 1;

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('orders2csv/columns/column.phtml');
	}

	public function getItemCount()
	{
		return $this->_itemCount;
	}

	public function setItemCount($itemCount)
	{
		$this->_itemCount = max($this->_itemCount, $itemCount);
		return $this;
	}

	protected function _prepareLayout()
	{
		$this->setChild('delete_button',
		$this->getLayout()->createBlock('adminhtml/widget_button')
		->setData(array(
                    'label' => Mage::helper('orders2csv')->__('Delete Column'),
                    'class' => 'delete delete-file-column '
		))
		);

		return parent::_prepareLayout();
	}

	public function getAddButtonId()
	{
		$buttonId = $this->getLayout()
		->getBlock('orders2csv.admin.file.columns')
		->getChild('add_button')->getId();
		return $buttonId;
	}

	public function getDeleteButtonHtml()
	{
		return $this->getChildHtml('delete_button');
	}

	public function getColumnValues()
	{
		$columns = Mage::getResourceModel('orders2csv/column');
		$columnsList = $columns->getColumnByFile($this->getRequest()->getParam('id'));

		$values = array();
		foreach ($columnsList as $column) {
// 			print_r($column);
// 			exit;
			
			$this->setItemCount($column['column_id']);

			$value = array();

			$value['column_id'] = $column['column_id'];
			$value['title'] = $column['title'];
			$value['sort_order'] = $column['sort_order'];
			$value['value'] = $column['value'];
			$value['id'] = $column['column_id'];
			$value['item_count'] = $this->getItemCount();
			$values[] = new Varien_Object($value);
		}
		return $values;
	}
	
	public function getValueOptions() {
		$valueOptions = Mage::helper('orders2csv')->getValueOptions();	
		return $valueOptions;
	}

}
