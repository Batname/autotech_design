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
class AW_Advancedreports_Block_Advanced_Purchased_Grid extends AW_Advancedreports_Block_Advanced_Grid
{
    protected $_routeOption = AW_Advancedreports_Helper_Data::ROUTE_ADVANCED_PURCHASED;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate( Mage::helper('advancedreports')->getGridTemplate() );
        $this->setExportVisibility(true);
        $this->setStoreSwitcherVisibility(true);
        $this->setId('gridPurchased');
    }

    public function hasRecords()
    {
		return false;
        return (count( $this->_customData ))
               && Mage::helper('advancedreports')->getChartParams( $this->_routeOption )
               && count( Mage::helper('advancedreports')->getChartParams( $this->_routeOption ) );
    }    


    public function addOrderItemsCount()
    {
		$itemTable = $this->getTable('sales_flat_order_item');

        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $this->getCollection()->getSelect()
                    ->join( array('item'=>$itemTable), "(item.order_id = main_table.entity_id AND item.parent_item_id IS NULL)", array( 'sum_qty' => 'SUM(item.qty_ordered)'))
                    ->where("main_table.entity_id = item.order_id")
                    ->group('main_table.entity_id')
                    ;
        } else {
            $this->getCollection()->getSelect()
                    ->join( array('item'=>$itemTable), "(item.order_id = e.entity_id AND item.parent_item_id IS NULL)", array( 'sum_qty' => 'SUM(item.qty_ordered)'))
                    ->where("e.entity_id = item.order_id")
                    ->group('e.entity_id')
                    ;
        }
		return $this;

    }

    protected function _prepareCollection()
    {
		parent::_prepareCollection();
		$this->addOrderItemsCount();
		$this->_prepareData();
		return $this;
    }

    protected function _addCustomData($row)
    {
        if ( count( $this->_customData ) )
        {
            foreach ( $this->_customData as &$d )
            {
                if ( isset( $d['customers'] ) && ($d['sum_qty'] == $row['sum_qty']) )
                {
					$customers = $d['customers'];
					unset($d['customers']);
					$d['customers'] = $customers + 1;
					return $this;
                }
            }
        }
        $this->_customData[] = $row;
        return $this;
    }

    /*
     * Prepare data array for Pie and Grid
     */
    protected function _prepareData()           
    {
//		echo $this->getCollection()->getSelect()->__toString();
        foreach ( $this->getCollection() as $order )
        {
            $row = array();

            foreach ($this->_columns as $column)
            {
                if ( !$column->getIsSystem() )
                {
                    $row[ $column->getIndex() ] = $order->getData( $column->getIndex() );
                }
            }
			$row['customers'] = 1;
			$row['title'] = round($row['sum_qty']);
            $this->_addCustomData($row);
        }

//        var_dump( $this->_customData );

        if ( ! count( $this->_customData ) )
        {
            return $this;
        }

		usort($this->_customData, array(&$this, "_compareQtyElements") );
        Mage::helper('advancedreports')->setChartData( $this->_customData, Mage::helper('advancedreports')->getDataKey( $this->_routeOption ) );
		parent::_prepareData();
        return $this;
    }

    /*
     * Need to sort bestsellers array
     */
    protected function _compareQtyElements($a, $b)
    {
        if ($a['sum_qty'] == $b['sum_qty'])
        {
            return 0;
        }
        return ($a['sum_qty'] > $b['sum_qty']) ? -1 : 1;
    }


    protected function _prepareColumns()
    {
        $this->addColumn('sum_qty', array(
            'header'    =>Mage::helper('advancedreports')->__('Products Purchased'),
            'align'     =>'right',
            'index'     =>'sum_qty',
            'total'     =>'sum',
            'type'      =>'number'
        ));
				
        $this->addColumn('customers', array(
            'header'    =>Mage::helper('advancedreports')->__('Number of Customers'),
            'align'     =>'right',
            'index'     =>'customers',
            'total'     =>'sum',
            'type'      =>'number'
        ));		
		
        $this->addExportType('*/*/exportOrderedCsv', Mage::helper('advancedreports')->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', Mage::helper('advancedreports')->__('Excel'));

        return $this;
    }

    public function getChartType()
    {
        return AW_Advancedreports_Block_Chart::CHART_TYPE_PIE3D;
    }
	
}