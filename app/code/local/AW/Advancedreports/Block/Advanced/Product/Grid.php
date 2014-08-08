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
class AW_Advancedreports_Block_Advanced_Product_Grid extends AW_Advancedreports_Block_Advanced_Grid
{
    protected $_routeOption = AW_Advancedreports_Helper_Data::ROUTE_ADVANCED_PRODUCTS;
    protected $_skus = array();
    protected $_filterSkus = array();
    protected $_skuColumns = array();


    public function __construct()
    {
        parent::__construct();
        $this->setTemplate( Mage::helper('advancedreports')->getGridTemplate() );
        $this->setExportVisibility(true);
        $this->setStoreSwitcherVisibility(true);
        $this->setId('gridProduct');
    }

    protected function _prepareGrid()
    {
        $this->_prepareMassactionBlock();
        $this->_prepareCollection();
        $this->_prepareColumns();
		parent::_prepareData();
        return $this;
    }

    protected function _prepareLayout()
    {
        # prepare SKUs
        if ( $filter = $this->getParam($this->getVarNameFilter(), null) )
        {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);
//            parse_str($filter, $data);
            if ( isset( $data['product_sku'] ) )
            {
                $this->setSkus( $data['product_sku'] );
            }
            Mage::helper('advancedreports')->setSkus( $data['product_sku'] );
        }
        else
        {
            if ( $skus = Mage::helper('advancedreports')->getSkus() )
            {
                $this->setSkus( $skus );
            }
        }
        parent::_prepareLayout();
        return $this;
    }

    public function getDisableAutoload()
    {
        return true;
    }

    public function getHideShowBy()
    {
        return false;
    }

    public function getIsSalesByProduct()
    {
        return true;
    }

    protected function _addCustomData($row)
    {
        $key = $this->getFilter('reload_key');
        if ( count( $this->_customData ) )
        {
            foreach ( $this->_customData as &$d )
            {
                if ( $d['period'] == $row['period'] )
                {
                    if ( isset( $d[$row['sku']] ) )
                    {
                        $qty = $d[ $row['sku'] ];
                        unset($d[ $row['sku'] ]);
						if ( isset( $d[ $row['column_id'] ] ) )
						{
						    unset($d[ $row['column_id'] ]);
						}
			
                        if ($key === 'total')
                        {
                            $d[ $row['sku'] ] = $row['total'] + $qty;
                            $d[ $row['column_id'] ] = $row['total'] + $qty;
                        }
                        else
                        {
                            $d[ $row['sku'] ] = $row['ordered_qty'] + $qty;
                            $d[ $row['column_id'] ] = $row['ordered_qty'] + $qty;
                        }
                        
                    }
                    else
                    {
                        if ($key === 'total')
                        {
                            $d[ $row['sku'] ] = $row['total'];
                            $d[ $row['column_id'] ] = $row['total'];
                        }
                        else
                        {
                            $d[ $row['sku'] ] = $row['ordered_qty'];
                            $d[ $row['column_id'] ] = $row['ordered_qty'];
                        }                        
                    }
                    return $this;
                }
            }
        }
        $this->_customData[] = $row;
        return $this;
    }

    protected function _isValidSku($sku)
    {
        return $this->_getProductName($sku);
    }

    protected function _getProductName($sku)
    {
        return Mage::helper('advancedreports')->getProductNameBySku($sku);
    }

    public function setSkus($value)
    {
        # parse SKUs string
		$filterSkus = array();
        $validSkus = array();
		$skuColumns = array();
        $skus = explode(',', $value);
        if ($skus)
        {
	    $i = 0;
            foreach ($skus as $sku)
            {
                if ( trim($sku) && $this->_isValidSku(trim($sku)) )
                {
				    $sku = strtolower(trim($sku));
		            $validSkus[] = $sku;
		            $filterSkus[] = Mage::helper('advancedreports')->getProductSkuBySku($sku);
				    $skuColumns[$sku] = 'column'.$i;
				    $i++;
                }
            }
        }      
        $this->_skus = $validSkus;
		$this->_skuColumns = $skuColumns;
		$this->_filterSkus = $filterSkus;
    }

    public function getColumnBySku($sku)
    {
		if ( $sku && isset( $this->_skuColumns[$sku] ) )
		{
		    return $this->_skuColumns[$sku];
		}	
    }

    public function getSkus()
    {
        return $this->_skus;
    }

    public function _prepareCollection()
    {        
        parent::_prepareOlderCollection();
        $this->getCollection()
            ->initReport('reports/product_ordered_collection');
        $this->_prepareData();
        return $this;
    }

    /*
     *  This part build collection for every period filter by entered skus.
     *  It's more optimal variant for oprimisation
     */
    protected function _setSkusFilter($collection)
    {
		if ($filter = $this->_getWhereSkusFilter())
		{
		    $collection->getSelect()->where("({$filter})");
		}
		return $this;
    }

    protected function _setStateFilter($collection)
    {
//		$entityValues = $collection->getTable('sales_order_varchar');
//		$entityAtribute = $collection->getTable('eav_attribute');
//		$collection->getSelect()
//				->join( array('attr'=>$entityAtribute), "attr.attribute_code = 'status'", array())
//				->join( array('val'=>$entityValues), "attr.attribute_id = val.attribute_id AND ".$this->_getProcessStates()." AND e.entity_id = val.entity_id", array())
//				;
		//TODO Clear # after tests
		$collection->addAttributeToFilter('status', explode( ",", Mage::helper('advancedreports')->confProcessOrders() ));

		return $this;
    }

    protected function _setPeriodFilter($collection, $from, $to)
    {
		$collection->getSelect()
	                        ->where("e.created_at >= ?", $from)
	                        ->where("e.created_at <= ?", $to);
		return $this;
    }
    
    protected function _setStoreFilter($collection, $storeIds)
    {
		$collection->getSelect()
				->where("e.store_id in ('".implode("','", $storeIds)."')");
		return $this;			
    }

    protected function _getWhereSkusFilter()
    {
		if ( count($this->_filterSkus) )
		{
		    $filter = '';
		    $is_first = true;
		    foreach($this->_filterSkus as $sku)
		    {
			if (!$is_first)
			{
			    $filter .= ' OR ';
			}
			$filter .= "item.sku = '{$sku}'";
			$is_first = false;
		    }
		    return $filter;
		}
		return null;
    }
    
    protected function _addItems($collection)
    {
		$itemTable = $this->getTable('sales_flat_order_item');
		$collection->getSelect()
				->join( array('item'=>$itemTable), "e.entity_id = item.order_id AND item.parent_item_id IS NULL", array( 'sum_qty' => 'SUM(item.qty_ordered)',  'sum_total' => 'SUM(item.base_row_total)', 'name' => 'name', 'sku'=>'sku' ) )
				->group('item.sku');
		return $this;
    }

    protected function _getOrderCollection($from, $to)
    {
		$collection = Mage::getModel('sales/order')->getCollection();

        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $orderTable = $this->getTable('sales_flat_order');
        } else {
            $orderTable = $this->getTable('sales_order');
        }
        
		$collection->getSelect()->reset();
		$collection->getSelect()->from(array('e'=>$orderTable), array());
	
		$this->_setPeriodFilter($collection, $from, $to)
		    ->_setSkusFilter($collection)
		    ->_setStateFilter($collection)
		    ->_addItems($collection);
	
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
		return $collection;
    }

    protected function _prepareData()
    {
		$chartLabels = array();
        if ( count($this->getSkus()) )
        {
            # primary analise
            foreach ( $this->getCollection()->getIntervals() as $_index=>$_item )
            {
                $items = $this->_getOrderCollection($_item['start'], $_item['end']);
//                echo $items->getSelect()->__toString().'<hr>';
				$row['period'] = $_item['title'];
                $this->_addCustomData($row);
                foreach ( $items as $item )
                {
                    if ( in_array( strtolower($item->getSku()), $this->_skus ) )
                    {
                        $row['period'] = $_item['title'];
                        $row['sku'] = strtolower($item->getSku());
						$row['column_id'] = $this->getColumnBySku( strtolower($item->getSku()) );
                        $row['ordered_qty'] = $item->getSumQty();
                        $row['total'] = $item->getSumTotal();
                        $this->_addCustomData($row);
                    }
                }
            }

            # final preporation of data
            if ( count( $this->_customData ) )
            {
                foreach ( $this->getSkus() as $sku )
                {
                    foreach ( $this->_customData as &$d )
                    {
                        if ( !isset( $d[$sku] ) )
                        {
                            $d[$sku] = 0;
                        }
                    }
                }                                
            }

		    foreach ($this->_skus as $sku)
		    {
				$chartLabels[$sku] = Mage::helper('advancedreports')->getProductNameBySku($sku);
		    }
        }
//		echo '<pre>';
//		var_dump($this->_skus);
//		var_dump($this->_skuColumns);
//		var_dump($this->_customData);
	//	echo '</pre>';
        Mage::helper('advancedreports')->setChartData( $this->_customData, Mage::helper('advancedreports')->getDataKey( $this->_routeOption ) );
        Mage::helper('advancedreports')->setChartKeys( $this->_skus, Mage::helper('advancedreports')->getDataKey( $this->_routeOption )  );
		Mage::helper('advancedreports')->setChartLabels( $chartLabels, Mage::helper('advancedreports')->getDataKey( $this->_routeOption )  );
		parent::_prepareData();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('periods', array(
            'header'    =>$this->getPeriodText(),
            'width'     =>'120px',
            'index'     =>'period',
            'type'      =>'text',
			'sortable'	=> false,
        ));

        $key = $this->getFilter('reload_key');
        $def_value = sprintf("%f", 0);
        $def_value = Mage::app()->getLocale()->currency($this->getCurrentCurrencyCode())->toCurrency($def_value);
        $def_value = $key === 'total' ? $def_value : '0';
        $type = $key === 'total' ? 'currency' : 'number';
        foreach ( $this->_skus as $sku )
        {
            $this->addColumn( $this->getColumnBySku($sku) , array(
                'header'    =>$this->_getProductName($sku),
                'index'     =>$this->getColumnBySku($sku),
                'type'      =>$type,
                'currency_code' => $this->getCurrentCurrencyCode(),
                'default'  => $def_value,
            ));
        }
        $this->addExportType('*/*/exportOrderedCsv', Mage::helper('advancedreports')->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', Mage::helper('advancedreports')->__('Excel'));

        return $this;
    }

    public function getChartType()
    {
        return AW_Advancedreports_Block_Chart::CHART_TYPE_MULTY_LINE;
    }

    public function getPeriods()
    {
        return parent::_getOlderPeriods();
    }

}
