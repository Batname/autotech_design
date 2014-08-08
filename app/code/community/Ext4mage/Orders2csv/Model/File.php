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
class Ext4mage_Orders2csv_Model_File extends Mage_Core_Model_Abstract
{

	protected $_column = array();
	protected $_columnInstance;

	public function _construct()
	{
		parent::_construct();
		$this->_init('orders2csv/file');
		
	}

	/**
	 * Retrieve active files for form
	 *
	 * @return array
	 */
	public function getFilesForForm()
	{
		$options = array();
		$allElements = Mage::getResourceModel('orders2csv/file')->getFileByActive();

		$options[] = array(
            'label' => Mage::helper('orders2csv')->__('Select file structure'),
            'value' => 0
		);

		foreach ($allElements as $element) {
			$options[] = array(
                'label' => $element['title'],
                'value' => $element['file_id']
			);
		}
		return $options;
	}

	/**
	 * Retrieve column instance
	 *
	 * @return Ext4mage_Orders2csv_Model_Column
	 */
	public function getColumnInstance()
	{
		if (!$this->_columnInstance) {
			$this->_columnInstance = Mage::getSingleton('orders2csv/column');
		}
		return $this->_columnInstance;
	}

	/**
	 * Retrieve column collection of file
	 *
	 * @return Ext4mage_Orders2csv_Model_Mysql4_Column_Collection
	 */
	public function getFileColumnCollection()
	{
		$collection = $this->getColumnInstance()->getColumnByFile($this);

		return $collection;
	}

	/**
	 * Add column to array of file columns
	 *
	 * @param Ext4mage_Orders2csv_Model_Column $column
	 * @return Ext4mage_Orders2csv_Model_File
	 */
	public function addColumn(Ext4mage_Orders2csv_Model_Column $column)
	{
		$this->_column[$column->getId()] = $column;
		return $this;
	}

	/**
	 * Get column from columns array of file by given id
	 *
	 * @param int $columnId
	 * @return Ext4mage_Orders2csv_Model_Column | null
	 */
	public function getColumnById($columnId)
	{
		if (isset($this->_column[$columnId])) {
			return $this->_column[$columnId];
		}

		return null;
	}

	/**
	 * Get all columns of file
	 *
	 * @return array
	 */
	public function getColumns()
	{
		return $this->_column;
	}

	/**
	 * Load file columns
	 *
	 * @return Ext4mage_Orders2csv_Model_File
	 */
	protected function _afterLoad()
	{
		parent::_afterLoad();

		/**
		 * Load columns
		 */
		foreach ($this->getFileColumnCollection() as $column) {
			$this->addColumn($column);
		}
		return $this;
	}

}