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
class AW_Advancedreports_Block_Advanced_Bestsellers_Grid extends AW_Advancedreports_Block_Advanced_Grid
{   
    protected $_routeOption = AW_Advancedreports_Helper_Data::ROUTE_ADVANCED_BESTSELLERS;
    protected $_customData = array();
    protected $_bestsellerVarData;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate( Mage::helper('advancedreports')->getGridTemplate() );
        $this->setExportVisibility(true);
        $this->setStoreSwitcherVisibility(true);
        $this->setId('gridBestsellers');        
    }

    public function getRoute()
    {
        return $this->_routeOption;
    }
	
	protected function _get1400ProcessStates()
	{
		$states = explode( ",", Mage::helper('advancedreports')->confProcessOrders() );
		$is_first = true;
		$filter = "";
		foreach ($states as $state)
		{
		    if (!$is_first)
		    {
			$filter .= " OR ";
		    }
		    $filter .= "orders.status = '".$state."'";
		    $is_first = false;
		}
		return "(".$filter.")";		
	}

    /*
     * Filter collction by order status
     */
    public function setState()
    {
    	# It's a very bad style but in this case have no time to do it clear
    	# TODO Do it clear in future 
    	if (Mage::helper('advancedreports')->checkVersion('1.4.0.0')){
    		$this->getCollection()->getSelect()
				->where($this->_get1400ProcessStates())
			;
    	} else {
			$entityValues = $this->getCollection()->getTable('sales_order_varchar');
			$entityAtribute = $this->getCollection()->getTable('eav_attribute');
			$this->getCollection()->getSelect()
					->join( array('attr'=>$entityAtribute), "attr.attribute_code = 'status'", array())
					->join( array('val'=>$entityValues), "attr.attribute_id = val.attribute_id AND ".$this->_getProcessStates(), array())
					->where('orders.entity_id = val.entity_id')
			;    					
    	}		
		return $this;
    }

    /*
     * Filter collection by Date
     */
    public function setDateFilter($from, $to)
    {
		$this->getCollection()->getSelect()
	                        ->where("orders.created_at >= ?", $from)
	                        ->where("orders.created_at <= ?", $to);
		return $this;
    }

    /*
     * Filter collection by Store Ids
     */
    public function setStoreFilter($storeIds)
    {
		$this->getCollection()->getSelect()
				->where("orders.store_id in ('".implode("','", $storeIds)."')");
		return $this;
    }
    
    public function addOrderItems($limit = 10)
    {
		$itemTable = $this->getTable('sales_flat_order_item');
        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $orderTable = $this->getTable('sales_flat_order');
        } else {
            $orderTable = $this->getTable('sales_order');
        }
		$this->getCollection()->getSelect()		
				->join( array('item'=>$itemTable), "(item.product_id = e.entity_id AND item.parent_item_id IS NULL)", array( 'product_id' => 'product_id', 'sum_qty' => 'SUM(item.qty_ordered)',  'sum_total' => 'SUM(item.base_row_total)', 'name' => 'name', 'sku'=>'sku' ))
				->join( array('orders'=>$orderTable), "orders.entity_id = item.order_id", array())
				->group('e.entity_id')
				->limit( $limit );
		return $this;
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();

		$this->setCollection( Mage::getModel('catalog/product')->getCollection() );

        $date_from = $this->_getMysqlFromFormat($this->getFilter('report_from'));
        $date_to = $this->_getMysqlToFormat($this->getFilter('report_to'));

        $this->setDateFilter($date_from, $date_to)->setState();

        if ($this->getRequest()->getParam('store')) {
            $storeIds = array($this->getParam('store'));
        } else if ($this->getRequest()->getParam('website')){
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } else if ($this->getRequest()->getParam('group')){
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        }
        if (isset($storeIds))
        {
	    	$this->setStoreFilter($storeIds);
        }
		$this->addOrderItems(Mage::helper('advancedreports')->confBestsellersCount());
		$key = $this->getFilter('reload_key');
	        if ( $key === 'qty' )
		{
		    $this->orderByQty();
		}
		elseif ( $key === 'total' )
		{
		    $this->orderByTotal();
		}	
        $this->_prepareData();
    }

    public function getChartParams()
    {
        return Mage::helper('advancedreports')->getChartParams( $this->_routeOption );
    }

    public function getNeedReload()
    {
        return Mage::helper('advancedreports')->getNeedReload( $this->_routeOption );
    }
    
    public function hasRecords()
    {
        return (count( $this->_customData ))
               && Mage::helper('advancedreports')->getChartParams( $this->_routeOption )
               && count( Mage::helper('advancedreports')->getChartParams( $this->_routeOption ) );
    }    
    
    public function getBestsellerColumns()
    {
        return $_bestsellerColumns;
    }

    protected function _toHtml()
    {
//        $this->_prepareData();
        return parent::_toHtml();
    }

    public function getShowCustomGrid()
    {
        return true;
    }

    public function getHideNativeGrid()
    {
        return true;
    }

    public function getHideShowBy()
    {
        return true;
    }
    
    protected function _addBestsellerData($row)
    {
        if ( count( $this->_customData ) )
        {            
            foreach ( $this->_customData as &$d )
            {
                if ( $d['id'] === $row['id'] )
                {
                    $qty = $d['ordered_qty'];
                    $total = $d['total'];                    
                    unset($d['total']);
                    unset($d['ordered_qty']);
                    $d['total'] = $row['total'] + $total;
                    $d['ordered_qty'] = $row['ordered_qty'] + $qty;
                    return $this;
                }
            }
        }
        $this->_customData[] = $row;
        return $this;
    }

    /*
     * Need to sort bestsellers array
     */
    protected function _compareTotalElements($a, $b)
    {
        if ($a['total'] == $b['total'])
        {
            return 0;
        }
        return ($a['total'] > $b['total']) ? -1 : 1;
    }
    /*
     * Need to sort bestsellers array
     */
    protected function _compareQtyElements($a, $b)
    {
        if ($a['ordered_qty'] == $b['ordered_qty'])
        {
            return 0;
        }
        return ($a['ordered_qty'] > $b['ordered_qty']) ? -1 : 1;
    }

    /*
     * Prepare data array for Pie and Grid     
     */
    protected function _prepareData()
    {
        # Extract data from collection
//		echo $this->getCollection()->getSelect()->__toString();
		$col = $this->getCollection();
		if ($col && count($col) )
		{
		    foreach ( $col as  $_subItem )
		    {
			    $row = array();
			    # Get all colummns values
			    foreach ($this->_columns as $column)
			    {
				if (!$column->getIsSystem())
				{
				    $row[ $column->getIndex() ] = $column->getRowField($_subItem);
				}
			    }
			    # Add quantity
			    $row['ordered_qty'] = $_subItem->getSumQty();
			    # Add total
			    $row['total'] = $_subItem->getSumTotal();
			    # Add product id
			    $row['id'] = $_subItem->getProductId();
			    $this->_addBestsellerData( $row );
		    }	
		}

        if ( ! count( $this->_customData ) )
        {
            return $this;
        }

        $key = $this->getFilter('reload_key');
        if ( $key === 'qty' )
        {
            # Sort data
            usort($this->_customData, array(&$this, "_compareQtyElements") );
            # Splice array
            array_splice( $this->_customData, Mage::helper('advancedreports')->confBestsellersCount() );

            # All qty
            $qty = 0;
            foreach ( $this->_customData as $d )
            {
                $qty += $d['ordered_qty'];
            }
            foreach ( $this->_customData as $i=>&$d )
            {
                $d['order'] = $i + 1;
                $d['percent'] = round( $d['ordered_qty'] * 100 / $qty ).' %';
                $d['percent_data'] = round( $d['ordered_qty'] * 100 / $qty );
                //Add title
                $d['title'] = $d['name'].' ('.$d['percent'].')';
            }
        }
        elseif ($key === 'total')
        {
            //Sort data
            usort($this->_customData, array(&$this, "_compareTotalElements") );
            //Splice array
            array_splice( $this->_customData, Mage::helper('advancedreports')->confBestsellersCount() );

            //All qty
            $total = 0;
            foreach ( $this->_customData as $d )
            {
                $total += $d['total'];
            }
            foreach ( $this->_customData as $i=>&$d )
            {
                $d['order'] = $i + 1;
                $d['percent'] = round( $d['total'] * 100 / $total ).' %';
                $d['percent_data'] = round( $d['total'] * 100 / $total );
                //Add title
                $d['title'] = $d['name'].' ('.$d['percent'].')';
            }
        }
        else
        {
            return $this;
        }
  
        Mage::helper('advancedreports')->setChartData( $this->_customData, Mage::helper('advancedreports')->getDataKey( $this->_routeOption ) );
		parent::_prepareData();
        return $this;
    }

    public function getBestsellerData()
    {
        return $this->_customData;
    }

    public function getCustomVarData()
    {
        if ($this->_customVarData)
        {
            return $this->_customVarData;
        }
        foreach ($this->_customData as $d)
        {
            $obj = new Varien_Object();
            $obj->setData( $d );
            $this->_customVarData[] = $obj;
        }
        return $this->_customVarData;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order', array(
            'header'    =>Mage::helper('reports')->__('N'),
            'width'     =>'60px',
            'align'     =>'right',
            'index'     =>'order',
            'type'      =>'number',
			'sortable'  => false,
        ));

        $this->addColumn('sku', array(
            'header'    =>Mage::helper('reports')->__('SKU'),
            'width'     =>'120px',
            'index'     =>'sku',
            'type'      =>'text',
			'sortable'  => false,
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'name',
	    	'type'      =>'text',
			'sortable'  => false,
        ));

        $this->addColumn('percent', array(
            'header'    =>Mage::helper('advancedreports')->__('Percent'),
            'width'     =>'60px',
            'align'     =>'right',
            'index'     =>'percent',
            'type'      =>'text',
			'sortable'  => false,
        ));

        $this->addColumn('ordered_qty', array(
            'header'    =>Mage::helper('advancedreports')->__('Quantity'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'ordered_qty',
            'total'     =>'sum',
            'type'      =>'number',
			'sortable'  => false,
        ));

        $this->addColumn('total', array(
            'header'    =>Mage::helper('reports')->__('Total'),
            'width'     =>'120px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total'     =>'sum',
            'index'     =>'total',
			'sortable'  => false,
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'align'     =>'right',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('advancedreports')->__('View'),
                        'url'     => array(
                            'base'=>'adminhtml/catalog_product/edit',
                            'params'=>array()
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        $this->addExportType('*/*/exportOrderedCsv', Mage::helper('advancedreports')->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', Mage::helper('advancedreports')->__('Excel'));

        return $this;
    }

    public function getChartType()
    {
        return AW_Advancedreports_Block_Chart::CHART_TYPE_PIE3D;
    }

    public function getRowUrl($row)
    {
        //return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId() ));
    }

    public function getExcel($filename = '')
    {
        $this->_prepareGrid();

        $data = array();
        foreach ($this->_columns as $column)
        {
            if (!$column->getIsSystem() && $column->getIndex() != 'stores')
            {
                $row[] = $column->getHeader();
            }
        }
        $data[] = $row;

        if (count($this->getCustomVarData())){
            foreach ($this->getCustomVarData() as $obj)
            {
                $row = array();
                foreach ($this->_columns as $column)
                {
                    if (!$column->getIsSystem() && $column->getIndex() != 'stores')
                    {
                        $row[] = $column->getRowField($obj);
                    }
                }
                $data[] = $row;
            }
        }

        $xmlObj = new Varien_Convert_Parser_Xml_Excel();
        $xmlObj->setVar('single_sheet', $filename);
        $xmlObj->setData($data);        
        $xmlObj->unparse();

        return $xmlObj->getData();
    }

    public function getCsv($filename = '')
    {
        $csv = '';
        $this->_prepareGrid();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem() && $column->getIndex() != 'stores') {
                $data[] = '"'.$column->getHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        if (!count($this->getCustomVarData())){
            return $csv;
        }

        foreach ($this->getCustomVarData() as $obj)
        {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem() && $column->getIndex() != 'stores') {
                    $data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowField($obj)).'"';
                }
            }  
            $csv.= implode(',', $data)."\n";
        }
        return $csv;
    }

}
