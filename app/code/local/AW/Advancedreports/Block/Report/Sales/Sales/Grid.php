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
 */?>
<?php
class AW_Advancedreports_Block_Report_Sales_Sales_Grid extends AW_Advancedreports_Block_Advanced_Grid
{
    protected $_routeOption = AW_Advancedreports_Helper_Data::ROUTE_SALES_SALES;
	protected $_reportCollections = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate( Mage::helper('advancedreports')->getGridTemplate() );
        $this->setExportVisibility(true);
        $this->setStoreSwitcherVisibility(true);
        $this->setId('gridStandardsales');
    }

    public function getRoute()
    {
        return $this->_routeOption;
    }

    public function hasRecords()
    {
        return (count( $this->getCollection()->getIntervals() ) > 1)
               && Mage::helper('advancedreports')->getChartParams( $this->_routeOption )
               && count( Mage::helper('advancedreports')->getChartParams( $this->_routeOption ) );
    }

    public function getChartParams()
    {
        return Mage::helper('advancedreports')->getChartParams( $this->_routeOption );
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();
//        $this->_prepareData();
        return $html;
    }
	
    public function getHideShowBy()
    {
        return false;
    }	
	
    public function getHideNativeGrid()
    {
        return false;
    }	
	
    public function getShowCustomGrid()
    {
        return false;
    }	
	
    protected function _addCustomData($row)
    {
    	if (!isset($row['items'])){
    		$row['items'] = 0;			
    	}
		if (!isset($row['orders'])){
			$row['orders'] = 0;
		}		
		$this->_customData[] = $row;		
		return $this;
    }	
	
    public function _prepareCollection()
    {
        parent::_prepareOlderCollection();
		# This calculate collection of intervals
        $this->getCollection()
            ->initReport('reports/product_ordered_collection');
        $this->_prepareData();
        return $this;
    }	
	
    /*
     *  This part build collection for every period filter by entered skus.
     *  It's more optimal variant for oprimisation
     */
    protected function _setOrderStateFilter($collection)
    {   		
//		$entityValues = $this->getTable('sales_order_varchar');
//		$entityAtribute = $this->getTable('eav_attribute');
//		$collection->getSelect()
//				->join( array('attr'=>$entityAtribute), "attr.attribute_code = 'status'", array())
//				->join( array('val'=>$entityValues), "attr.attribute_id = val.attribute_id AND ".$this->_getProcessStates()." AND e.entity_id = val.entity_id", array())
//				;
				
		$collection->addAttributeToFilter('status', explode( ",", Mage::helper('advancedreports')->confProcessOrders() ));		
				
		return $this;
    }

    protected function _setStoreFilter($collection, $storeIds)
    {
		$collection->getSelect()
				->where("e.store_id in ('".implode("','", $storeIds)."')");
		return $this;
    }	
	
    protected function _addItems($collection)
    {
		$itemTable = $this->getTable('sales_flat_order_item');
		$collection->getSelect()
				->join( array('item'=>$itemTable), "e.entity_id = item.order_id AND item.parent_item_id IS NULL", array( 
					'items_count' => 'SUM(item.qty_ordered)',
				))				
				;
		return $this;
    }	
	
	protected function _getItemStatistics($from, $to)
	{
		$collection = Mage::getModel('sales/order')->getCollection();
        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $orderTable = $this->getTable('sales_flat_order');
        } else {
            $orderTable = $this->getTable('sales_order');
        }
		$collection->getSelect()->reset();
		$collection->getSelect()->from(array('e'=>$orderTable), array());		
		
		# set State filter
		$this->_setOrderStateFilter($collection);			
		
		$this
			->_addItems($collection)
		;		
		$collection->getSelect()->group('e.entity_id');

		#set data filter
		$collection->getSelect()
	                        ->where("e.created_at >= ?", $from)
	                        ->where("e.created_at <= ?", $to)
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
		    $this->_setStoreFilter($collection, $storeIds);
		}
				
//		echo $collection->getSelect()->__toString()."<hr />";
        
		$items = new Varien_Object(array('items_count'=>0));

      	if (count($collection)){
	 		foreach ($collection as $item){
                $items->setItemsCount( $items->getItemsCount() + $item->getItemsCount() );
	 		}
	 	}
        return $items;
	}			
	
	public function getReport($from, $to)
	{		
		$key = $from.' - '.$to;
		if (isset($this->_reportCollections[$key])){
			return $this->_reportCollections[$key];	
		}	
							
		$collection = Mage::getModel('sales/order')->getCollection();
		
        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $orderTable = $this->getTable('sales_flat_order');
        } else {
            $orderTable = $this->getTable('sales_order');
        }

		$itemTable = $this->getTable('sales_flat_order_item');
		$collection->getSelect()->reset();
		$collection->getSelect()->from(array('e'=>$orderTable), array(
				'orders' 	=> "COUNT(e.entity_id)", # Just because it's unique
				'subtotal'	=> "SUM(e.base_subtotal)",
				'tax'	=> "SUM(e.base_tax_amount)",
				'discount'	=> "SUM(e.base_discount_amount)",
				'shipping'	=> "SUM(e.base_shipping_amount)",
				'total'	=> "SUM(e.base_grand_total)",
				'invoiced'	=> "SUM(e.base_total_invoiced)",
				'refunded'	=> "SUM(e.base_total_refunded)",
                'int_1' => "ROUND(1)",
			));		
		
	
		$collection->getSelect()->group('int_1');
		
		
		
		#set data filter
		$collection->getSelect()
	                        ->where("e.created_at >= ?", $from)
	                        ->where("e.created_at <= ?", $to)
				;					
			
		# set State filter
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
		
//		echo $collection->getSelect()->__toString().'<hr />';
		
	 	if (count($collection)){
	 		foreach ($collection as $item){
		 		if ($items = $this->_getItemStatistics($from, $to)){
					$item->setItems($items->getItemsCount());
				}
	 		}
	 	}		
		
		$this->_reportCollections[$key] = $collection;	
		return $collection;
	}			

    protected function _prepareData()
    {
        //Remember available keys
        $keys = array();
        foreach ( $this->getChartParams() as $param )
        {
            $keys[] = $param['value'];
        }

        $dataKeys = array();
        foreach ( $this->_columns as $column )
        {
            if ( !$column->getIsSystem() && in_array($column->getIndex(), $keys) )
            {
                $dataKeys[] = $column->getIndex();
            }
        }
        //Get data
        $data = array();
        foreach ($this->getCollection()->getIntervals() as $_index=>$_item)
        {
            $report = $this->getReport($_item['start'], $_item['end']);
            $row = array();
            foreach ($report as $_subIndex=>$_subItem)
            {
                $row = array();
                foreach ($this->_columns as $column)
                {
                    $row[$column->getIndex()] = $_subItem->getData( $column->getIndex() );
                }          
            }        
						   
            $row['period'] = $_index;
            $data[] = $row;
			$this->_addCustomData($row);
        }
//		var_dump($this->_customData);
        if ($data)
        {
            Mage::helper('advancedreports')->setChartData( $data, Mage::helper('advancedreports')->getDataKey( $this->_routeOption ) );
        }
		parent::_prepareData();
    }

    protected function _prepareColumns()
    {	
        $this->addColumn('orders', array(
            'header'    =>Mage::helper('reports')->__('Number of Orders'),
            'index'     =>'orders',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $this->addColumn('items', array(
            'header'    =>Mage::helper('reports')->__('Items Ordered'),
            'index'     =>'items',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $currency_code = $this->getCurrentCurrencyCode();

        $this->addColumn('subtotal', array(
            'header'    =>Mage::helper('reports')->__('Subtotal'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'subtotal',
            'total'     =>'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('tax', array(
            'header'    =>Mage::helper('reports')->__('Tax'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'tax',
            'total'     =>'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('shipping', array(
            'header'    =>Mage::helper('reports')->__('Shipping'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'shipping',
            'total'     =>'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('discount', array(
            'header'    =>Mage::helper('reports')->__('Discounts'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'discount',
            'total'     =>'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('total', array(
            'header'    =>Mage::helper('reports')->__('Total'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'total',
            'total'     =>'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('invoiced', array(
            'header'    =>Mage::helper('reports')->__('Invoiced'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'invoiced',
            'total'     =>'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('refunded', array(
            'header'    =>Mage::helper('reports')->__('Refunded'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'refunded',
            'total'     =>'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addExportType('*/*/exportStandardsalesCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportStandardsalesExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }	

    public function getChartType()
    {
        return AW_Advancedreports_Block_Chart::CHART_TYPE_LINE;
    }
	
    public function getPeriods()
    {
        return parent::_getOlderPeriods();
    }	
	
	protected function _beforeExport()
	{
        $this->addColumn('periods', array(
            'header'    =>$this->getPeriodText(),
            'width'     =>'120px',
            'index'     =>'period',
            'type'      =>'text'
        ));		
	} 
	
    public function getExcel($filename = '')
    {
    	$this->_beforeExport();
		return parent::getExcel($filename);
    }

    public function getCsv($filename = '')
    {        
		$this->_beforeExport();
		return parent::getCsv($filename);
    }
	
}
