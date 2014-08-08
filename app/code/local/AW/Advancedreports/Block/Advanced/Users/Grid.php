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
 * @package    AW_Advancedreports
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *//**
 * User Activity Report Grid
 */
class AW_Advancedreports_Block_Advanced_Users_Grid extends AW_Advancedreports_Block_Advanced_Grid
{
    /**
     * Grid Options Route Key
     * @var string
     */
    protected $_routeOption = AW_Advancedreports_Helper_Data::ROUTE_ADVANCED_USERS;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate( Mage::helper('advancedreports')->getGridTemplate() );
        $this->setExportVisibility(true);
        $this->setStoreSwitcherVisibility(true);
        $this->setId('gridUsers');
    }

    /**
     * Flag to show Show By select
     * @return boolean
     */
    public function getHideShowBy()
    {
        return false;
    }

    /**
     * Add Custom Data row
     * @param array $row
     * @return AW_Advancedreports_Block_Advanced_Users_Grid
     */
    protected function _addCustomData($row)
    {
		$this->_customData[] = $row;
		return $this;
    }

    /**
     * Prepare grid collection
     * @return AW_Advancedreports_Block_Advanced_Users_Grid
     */
    public function _prepareCollection()
    {
        parent::_prepareOlderCollection();
		# This calculate collection of intervals
        $this->getCollection()
            ->initReport('reports/product_ordered_collection');
        $this->_prepareData();
        return $this;
    }

    /**
     * Set up Order Status filtyer to collection
     * @param Mage_Reports_Model_Mysql4_Report_Collection $collection
     * @return AW_Advancedreports_Block_Advanced_Users_Grid
     */
    protected function _setOrderStateFilter($collection)
    {
        if (Mage::helper('advancedreports')->checkVersion('1.4.0.0')){
            $collection->addAttributeToFilter('status', explode( ",", Mage::helper('advancedreports')->confProcessOrders() ));
        } else {
            $entityValues = $collection->getTable('sales_order_varchar');
            $entityAtribute = $collection->getTable('eav_attribute');
            $collection->getSelect()
                    ->join( array('attr'=>$entityAtribute), "attr.attribute_code = 'status'", array())
                    ->join( array('val'=>$entityValues), "attr.attribute_id = val.attribute_id AND ".$this->_getProcessStates()." AND e.entity_id = val.entity_id", array())
                    ;
        }               
		return $this;
    }

    /**
     * Set up Store filter to collection
     * @param Mage_Reports_Model_Mysql4_Report_Collection $collection
     * @param int|array $storeIds
     * @return AW_Advancedreports_Block_Advanced_Users_Grid
     */
    protected function _setStoreFilter($collection, $storeIds)
    {
        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $collection->getSelect()
                    ->where("store_id in ('".implode("','", $storeIds)."')");
        } else {
            $collection->getSelect()
                    ->where("e.store_id in ('".implode("','", $storeIds)."')");
        }
		return $this;
    }

    /**
     * Retrives orders count for period
     * @param Datetime $from
     * @param Datetime $to
     * @return int
     */
    protected function _getOrdersCount($from, $to)
    {
		$collection = Mage::getModel('sales/order')->getCollection();

		#set data filter
        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $collection->getSelect()
                                ->where("main_table.created_at >= ?", $from)
                                ->where("main_table.created_at <= ?", $to);
                    ;
        } else {
            $collection->getSelect()
                                ->where("e.created_at >= ?", $from)
                                ->where("e.created_at <= ?", $to);
                    ;
        }

		#set order state filter
		$this->_setOrderStateFilter($collection);
	
		# check Store Filter
		if ($this->getRequest()->getParam('store')) {
		    $storeIds = array($this->getParam('store'));
		} else if ($this->getRequest()->getParam('website')){
		    $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
		} else if ($this->getRequest()->getParam('group')){
		    $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
		}
		if (isset($storeIds))
		{
	   	    $this->_setStoreFilter($collection, $storeIds);
		}
		return $collection->getSize();
	}

    /**
     * Retrives new accounts count for period
     * @param Datetime $from
     * @param Datetime $to
     * @return int
     */
	protected function _getAccountsCount($from, $to)
	{
		$collection = Mage::getModel('customer/customer')->getCollection();
	
		#set data filter
		$collection->getSelect()
	                        ->where("e.created_at >= ?", $from)
	                        ->where("e.created_at <= ?", $to);
				;
	
		#set order state filter
	//	$this->_setOrderStateFilter($collection);
	
		# check Store Filter
		if ($this->getRequest()->getParam('store')) {
		    $storeIds = array($this->getParam('store'));
		} else if ($this->getRequest()->getParam('website')){
		    $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
		} else if ($this->getRequest()->getParam('group')){
		    $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
		}
		if (isset($storeIds))
		{
	   	    $this->_setStoreFilter($collection, $storeIds);
		}
		
	//	echo $collection->getSelect()->__toString().'<hr>';
		return $collection->getSize();
    }

    /**
     * Retrives reviews count for period
     * @param Datetime $from
     * @param Datetime $to
     * @return int
     */
    protected function _getReviewsCount($from, $to)
    {
		$collection = Mage::getModel('review/review')->getCollection();
	
		#set data filter
		$collection->getSelect()
	                        ->where("main_table.created_at >= ?", $from)
	                        ->where("main_table.created_at <= ?", $to);
				;
	
		# check Store Filter
		if ($this->getRequest()->getParam('store')) {
		    $storeIds = array($this->getParam('store'));
		} else if ($this->getRequest()->getParam('website')){
		    $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
		} else if ($this->getRequest()->getParam('group')){
		    $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
		}
		if (isset($storeIds))
		{
		    $collection->addStoreFilter($this->getStoreIds($storeIds));
		}
		return $collection->getSize();
    }

    /**
     * Prepare data for Chart and Grid
     * @return AW_Advancedreports_Block_Advanced_Users_Grid
     */
    protected function _prepareData()
    {

		# primary analise
		foreach ( $this->getCollection()->getIntervals() as $_index=>$_item )
		{
		    $row['period'] = $_item['title'];
	
		    $row['accounts'] = $this->_getAccountsCount($_item['start'], $_item['end']);
		    $row['orders'] = $this->_getOrdersCount($_item['start'], $_item['end']);
		    $row['reviews'] = $this->_getReviewsCount($_item['start'], $_item['end']);
		    $this->_addCustomData($row);
		}
	
		$chartLabels = array('accounts' => Mage::helper('advancedreports')->__('New Accounts'),
				     'orders'	=> Mage::helper('advancedreports')->__('Orders'),
				     'reviews'	=> Mage::helper('advancedreports')->__('Reviews') );
		$keys = array();
		foreach ($chartLabels as $key=>$value)
		{
		    $keys[] = $key;
		}
		
	//	echo '<pre>';
	//	var_dump($chartLabels);
	//	var_dump($this->_customData);
	//	echo '</pre>';		
	
        Mage::helper('advancedreports')->setChartData( $this->_customData, Mage::helper('advancedreports')->getDataKey( $this->_routeOption ) );
        Mage::helper('advancedreports')->setChartKeys( $keys, Mage::helper('advancedreports')->getDataKey( $this->_routeOption )  );
        Mage::helper('advancedreports')->setChartLabels( $chartLabels, Mage::helper('advancedreports')->getDataKey( $this->_routeOption )  );
		parent::_prepareData();
        return $this;
    }

    /**
     * Prepare columns for grid
     * @return AW_Advancedreports_Block_Advanced_Users_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('periods', array(
            'header'    =>$this->getPeriodText(),
            'width'     =>'120px',
            'index'     =>'period',
            'type'      =>'text'
        ));

        $this->addColumn('accounts', array(
            'header'    =>Mage::helper('advancedreports')->__('New Accounts'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'accounts',
            'type'      =>'number',
	    'default'   =>'0'
        ));

        $this->addColumn('orders', array(
            'header'    =>Mage::helper('advancedreports')->__('Orders'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'orders',
            'type'      =>'number',
	    'default'   =>'0'
        ));

        $this->addColumn('reviews', array(
            'header'    =>Mage::helper('advancedreports')->__('Reviews'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'reviews',
            'type'      =>'number',
	    'default'   =>'0'
        ));

        $this->addExportType('*/*/exportOrderedCsv', Mage::helper('advancedreports')->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', Mage::helper('advancedreports')->__('Excel'));

        return $this;
    }

    /**
     * Retrives Chart type
     * @return string
     */
    public function getChartType()
    {
        return AW_Advancedreports_Block_Chart::CHART_TYPE_MULTY_LINE;
    }

    /**
     * Retrives older periods collection
     * @return Mage_Reports_Model_Mysql4_Report_Collection
     */
    public function getPeriods()
    {
        return parent::_getOlderPeriods();
    }

}
