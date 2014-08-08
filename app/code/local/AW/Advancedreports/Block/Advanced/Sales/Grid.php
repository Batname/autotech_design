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
class AW_Advancedreports_Block_Advanced_Sales_Grid extends AW_Advancedreports_Block_Advanced_Grid
{
    protected $_routeOption = AW_Advancedreports_Helper_Data::ROUTE_ADVANCED_SALES;
	protected $_optCollection;
	protected $_optCache = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate( Mage::helper('advancedreports')->getGridTemplate() );
        $this->setExportVisibility(true);
        $this->setStoreSwitcherVisibility(true);
		$this->setUseAjax(true);
		$this->setFilterVisibility(true);
        $this->setId('gridAdvancedSales');
    }

    public function getHideShowBy()
    {
        return true;
    }

    protected function _addCustomData($row)
    {
		if ($this->_filterPass($row)){
			$this->_customData[] = $row;
		}		
		return $this;
    }

    public function addOrderItems()
    {
		$itemTable = $this->getTable('sales_flat_order_item');
		$orderTable = $this->getTable('sales_order');
		$this->getCollection()->getSelect()
				->join( array('item'=>$itemTable), "(item.order_id = e.entity_id AND item.parent_item_id IS NULL)" )
				->joinLeft( array('item_simple' => $itemTable), "(item_simple.order_id = e.entity_id AND item.item_id = item_simple.parent_item_id)", array('simple_sku' => 'sku'))
				->order('e.created_at DESC')
				;
		return $this;
    }
	
	protected function _addManufacturer($collection)
	{	
		$entityProduct = $this->getTable('catalog_product_entity');
		$entityValuesVarchar = $this->getTable('catalog_product_entity_varchar');
		$entityValuesInt = $this->getTable('catalog_product_entity_int');
		$entityAtribute = $this->getTable('eav_attribute');
		$eavAttrOptVal = $this->getTable('eav_attribute_option_value');
		$collection->getSelect()
			->join( array( '_product'=>$entityProduct ), "_product.entity_id = item.product_id", array( 'p_product_id' => 'item.product_id' ) )
			->join( array( '_manAttr'=>$entityAtribute ), "_manAttr.attribute_code = 'manufacturer'", array() )	
			->joinLeft( array( '_manValVarchar'=>$entityValuesVarchar ), "_manValVarchar.attribute_id = _manAttr.attribute_id AND _manValVarchar.entity_id = _product.entity_id", array() )
			->joinLeft( array( '_manValInt'=>$entityValuesInt ), "_manValInt.attribute_id = _manAttr.attribute_id AND _manValInt.entity_id = _product.entity_id", array() )
			->joinLeft( array( '_optVal'=>$eavAttrOptVal ), "_optVal.option_id = IFNULL(_manValInt.value, _manValVarchar.value) AND _optVal.store_id = 0", array('product_manufacturer'=>'value') )
		;	
	}	
	
	protected function _addAddress($collection)
	{
        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $salesFlatOrderAddress = $this->getTable('sales_flat_order_address');
            $collection->getSelect()
                ->joinLeft(array('flat_order_addr_ship'=>$salesFlatOrderAddress), "flat_order_addr_ship.parent_id = e.entity_id AND flat_order_addr_ship.address_type = 'shipping'", array(
                        'order_ship_postcode' => 'postcode',
                        'order_ship_country_id' => 'country_id',
                        'order_ship_region' => 'region',
                        'order_ship_city' => 'city',
                    ))
                ->joinLeft(array('flat_order_addr_bil'=>$salesFlatOrderAddress), "flat_order_addr_bil.parent_id = e.entity_id AND flat_order_addr_bil.address_type = 'billing'", array(
                        'order_bil_postcode' => 'postcode',
                        'order_bil_country_id' => 'country_id',
                        'order_bil_region' => 'region',
                        'order_bil_city' => 'city',
                    ))
            ;
        } else {
            $entityValues = $this->getTable('sales_order_int');
            $entityAtribute = $this->getTable('eav_attribute');
            $entityType = $this->getTable('eav_entity_type');
            $salesFlatQuote = $this->getTable('sales_flat_quote');
            $salesFlatQuoteAddress = $this->getTable('sales_flat_quote_address');
            $collection->getSelect()
                ->joinLeft(array('a_type_order'=>$entityType), "a_type_order.entity_type_code='order'", array())
                ->joinLeft(array('a_attr_quote'=>$entityAtribute), "a_type_order.entity_type_id=a_attr_quote.entity_type_id AND a_attr_quote.attribute_code = 'quote_id'", array())
                ->joinLeft(array('a_value_quote'=>$entityValues), "a_value_quote.entity_id = e.entity_id AND a_value_quote.attribute_id = a_attr_quote.attribute_id", array())
                ->joinLeft(array('flat_quote'=>$salesFlatQuote), "flat_quote.entity_id = a_value_quote.value", array())
                ->joinLeft(array('flat_quote_addr_ship'=>$salesFlatQuoteAddress), "flat_quote_addr_ship.quote_id = flat_quote.entity_id AND flat_quote_addr_ship.address_type = 'shipping'", array(
                        'order_ship_postcode' => 'postcode',
                        'order_ship_country_id' => 'country_id',
                        'order_ship_region' => 'region',
                        'order_ship_city' => 'city',
                    ))
                ->joinLeft(array('flat_quote_addr_bil'=>$salesFlatQuoteAddress), "flat_quote_addr_bil.quote_id = flat_quote.entity_id AND flat_quote_addr_bil.address_type = 'billing'", array(
                        'order_bil_postcode' => 'postcode',
                        'order_bil_country_id' => 'country_id',
                        'order_bil_region' => 'region',
                        'order_bil_city' => 'city',
                    ))
            ;
        }
		return $this;		
	}	
	
    public function _prepareCollection()
    {
        parent::_prepareCollection();

		$collection = $this->getCollection();

        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $orderTable = $this->getTable('sales_flat_order');
        } else {
            $orderTable = $this->getTable('sales_order');
        }

		$collection->getSelect()->reset();
		$collection->getSelect()->from(array('e'=>$orderTable), array(
			'order_created_at' => 'created_at', 
			'order_id' => 'entity_id',
			'order_increment_id' => 'increment_id',
		));
		
		# Add address data to query
		$this->_addAddress($collection);
	
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
		$this->addOrderItems();
		$this->_addManufacturer($collection);
        $this->_prepareData();
    }

    /**
     * Set up store ids to filter collection
     * @param int|array $storeIds
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    public function setStoreFilter($storeIds)
    {
        $this->getCollection()->getSelect()
                ->where("e.store_id in ('".implode("','", $storeIds)."')");

		return $this;
    }

    /**
     * Set up date filter to collection of grid
     * @param Datetime $from
     * @param Datetime $to
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    public function setDateFilter($from, $to)
    {
        $this->getCollection()->getSelect()
                            ->where("e.created_at >= ?", $from)
                            ->where("e.created_at <= ?", $to);
		return $this;
    }

	protected function _addOptionToCache($id, $value)
	{
		$this->_optCache[$id] = $value;
	}
	
	protected function _optionInCache($id)
	{
		if (count($this->_optCache)){
			foreach ($this->_optCache as $key=>$value){
				if ($key == $id){
					return $value;
				}
			}
		}
	}
	
	protected function _getManufacturer( $option_id )
	{
		if (!$this->_optCollection)
		{
            $this->_optCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setStoreFilter(0, false)
                ->load();
		}
		# seach in quick cache
		if ($val = $this->_optionInCache($option_id)){
			return $val;
		}
		# search in chached collection
		foreach ($this->_optCollection as $item)
		{
            if ( $option_id == $item->getOptionId() ){
            	$this->_addOptionToCache($option_id, $item->getValue());
            	return $item->getValue();
            }
        }
		return null;
	}
	
    public function setState()
    {
    	if (Mage::helper('advancedreports')->checkVersion('1.4.0.0')){
    		$this->getCollection()->addAttributeToFilter('status', explode( ",", Mage::helper('advancedreports')->confProcessOrders() ));	
    	} else {
	 		$entityValues = $this->getCollection()->getTable('sales_order_varchar');
			$entityAtribute = $this->getCollection()->getTable('eav_attribute');
			$this->getCollection()->getSelect()
					->join( array('attr'=>$entityAtribute), "attr.attribute_code = 'status'", array())
					->join( array('val'=>$entityValues), "attr.attribute_id = val.attribute_id AND ".$this->_getProcessStates()." AND e.entity_id = val.entity_id", array())
					;   		
    	}		
		return $this;
    }	

    protected function _prepareData()
    {
//		echo $this->getCollection()->getSelect()->__toString();
		foreach ($this->getCollection() as $item)		
		{
			$row = $item->getData();
			if (isset( $row['order_ship_country_id'] )){
//				$row['order_ship_country'] = Mage::getSingleton('directory/country')->loadByCode( $row['order_ship_country_id'] )->getName();
				$row['order_ship_country'] = $row['order_ship_country_id'];	
			}		
			if (isset( $row['order_bil_country_id'] )){
//				$row['order_bil_country'] = Mage::getSingleton('directory/country')->loadByCode( $row['order_bil_country_id'] )->getName();
				$row['order_bil_country'] = $row['order_bil_country_id'];	
			}			
			
			# Billing/Shipping logic
			if (isset($row['order_ship_country'])){
				$row['order_country'] = $row['order_ship_country'];
			} elseif(isset($row['order_bil_country'])) {
				$row['order_country'] = $row['order_bil_country'];
			}
			if (isset($row['order_ship_region'])){
				$row['order_region'] = $row['order_ship_region'];
			} elseif(isset($row['order_bil_region'])) {
				$row['order_region'] = $row['order_bil_region'];
			}
			if (isset($row['order_ship_city'])){
				$row['order_city'] = $row['order_ship_city'];
			} elseif(isset($row['order_bil_city'])) {
				$row['order_city'] = $row['order_bil_city'];
			}			
			if (isset($row['order_ship_postcode'])){
				$row['order_postcode'] = $row['order_ship_postcode'];
			} elseif(isset($row['order_bil_postcode'])) {
				$row['order_postcode'] = $row['order_bil_postcode'];
			}
						
			$row['base_row_subtotal'] = $row['base_row_total'];
			$row['base_row_total'] = $row['base_row_total'] + $row['base_tax_amount'] - abs($row['base_discount_amount']);
			if ($row['base_row_total'] < 0){
				$row['base_row_total'] = 0;
			}
			if ($row['base_row_invoiced'] > $row['base_row_total']){
				$row['base_row_invoiced'] = $row['base_row_total'];
			}
			if (isset($row['base_row_refunded'])){
				if ($row['base_row_refunded'] > $row['base_row_total']){
					$row['base_row_refunded'] = $row['base_row_total'];
				}				
			} else {
				if ($row['base_amount_refunded'] > $row['base_row_total']){
					$row['base_row_refunded'] = $row['base_row_total'];
				} else {
					$row['base_row_refunded'] = $row['base_amount_refunded'];
				}
			}			
			
			if (isset($row['simple_sku'])){
				$row['sku'] = $row['simple_sku'];
			} 
//			elseif (isset($row['product_id'])){
//				
//			}
//			Varien_Profiler::start('aw:advancedreports::sales::prepare_data::load_product');
//			if (isset($row['product_id']) && $product = Mage::getSingleton('catalog/product')->load($row['product_id'])){
//				$opt_id = $product->getManufacturer();
//				$row['product_manufacturer'] = $this->_getManufacturer($opt_id);
//			}
//            Varien_Profiler::stop('aw:advancedreports::sales::prepare_data::load_product');

			if (isset($row['sku'])){
				$this->_addCustomData($row);
			}					    
		}
//        var_dump('speed:'.Varien_Profiler::fetch('aw:advancedreports::sales::prepare_data::load_product') );
//        var_dump('mem:'.Varien_Profiler::fetch('aw:advancedreports::sales::prepare_data::load_product', 'emalloc') );
//		echo '<pre>';
	//	var_dump($chartLabels);
//		var_dump($this->_customData);
//		echo '</pre>';		
		parent::_prepareData();
		$this->_setColumnFilters();        
        return $this;
    }

    protected function _prepareColumns()
    {	

        $def_value = sprintf("%f", 0);
        $def_value = Mage::app()->getLocale()->currency($this->getCurrentCurrencyCode())->toCurrency($def_value);	
	
        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('advancedreports')->__('Order #'),
            'index' => 'order_increment_id',
            'type' => 'text',
            'width' => '80px',
        ));    	
		
        $this->addColumn('order_created_at', array(
            'header' => Mage::helper('advancedreports')->__('Order Date'),
            'index' => 'order_created_at',
            'type' => 'datetime',
            'width' => '140px',
        ));

        $this->addColumn('sku', array(
            'header'    =>Mage::helper('advancedreports')->__('SKU'),
            'width'     =>'120px',
            'index'     =>'sku',
            'type'      =>'text'
        ));
		
        $this->addColumn('order_ship_country', array(
            'header' => Mage::helper('advancedreports')->__('Country'),
            'index' => 'order_ship_country',
            'type' => 'country',
            'width' => '100px',
        ));   		
		
        $this->addColumn('order_ship_region', array(
            'header' => Mage::helper('advancedreports')->__('Region'),
            'index' => 'order_ship_region',
            'type' => 'text',
            'width' => '100px',
        ));
		
        $this->addColumn('order_ship_city', array(
            'header' => Mage::helper('advancedreports')->__('City'),
            'index' => 'order_ship_city',
            'type' => 'text',
            'width' => '100px',
        ));   		   	
				
        $this->addColumn('order_ship_postcode', array(
            'header' => Mage::helper('advancedreports')->__('Zip Code'),
            'index' => 'order_ship_postcode',
            'type' => 'text',
            'width' => '60px',
        ));			

        $this->addColumn('name', array(
            'header'    =>Mage::helper('advancedreports')->__('Product Name'),
            'index'     =>'name',
	    'type'      =>'text'
        ));

        $this->addColumn('product_manufacturer', array(
            'header'    =>Mage::helper('advancedreports')->__('Manufacturer'),
            'index'     =>'product_manufacturer',
	    	'type'      =>'text',
			'width'     =>'100px',
        ));
		
        $this->addColumn('qty_ordered', array(
            'header'    =>Mage::helper('advancedreports')->__('Quantity'),
            'width'     =>'60px',
            'index'     =>'qty_ordered',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $this->addColumn('base_price', array(
            'header'    =>Mage::helper('advancedreports')->__('Price'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total'     =>'sum',
            'index'     =>'base_price',
			'column_css_class' => 'nowrap',
			'default'  => $def_value,
			'disable_total' => 1,
        ));					
		
        $this->addColumn('base_row_subtotal', array(
            'header'    =>Mage::helper('advancedreports')->__('Subtotal'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total'     =>'sum',
            'index'     =>'base_row_subtotal',
			'column_css_class' => 'nowrap',
			'default'  => $def_value,
        ));		
		
        $this->addColumn('base_tax_amount', array(
            'header'    =>Mage::helper('advancedreports')->__('Tax'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total'     =>'sum',
            'index'     =>'base_tax_amount',
			'column_css_class' => 'nowrap',
			'default'  => $def_value,
        ));				
		
        $this->addColumn('base_discount_amount', array(
            'header'    =>Mage::helper('advancedreports')->__('Discounts'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total'     =>'sum',
            'index'     =>'base_discount_amount',
			'column_css_class' => 'nowrap',
			'default'  => $def_value,
        ));				
								
        $this->addColumn('base_row_total', array(
            'header'    =>Mage::helper('advancedreports')->__('Total'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total'     =>'sum',
            'index'     =>'base_row_total',
			'column_css_class' => 'nowrap',
			'default'  => $def_value,
        ));						

        $this->addColumn('base_row_invoiced', array(
            'header'    =>Mage::helper('advancedreports')->__('Invoiced'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total'     =>'sum',
            'index'     =>'base_row_invoiced',
			'column_css_class' => 'nowrap',
			'default'  => $def_value,
        ));		
		
        $this->addColumn('base_amount_refunded', array(
            'header'    =>Mage::helper('advancedreports')->__('Refunded'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total'     =>'sum',
            'index'     =>'base_amount_refunded',
			'column_css_class' => 'nowrap',
			'default'  => $def_value,
        ));		

        $this->addColumn('view_order',
            array(
                'header'    => Mage::helper('advancedreports')->__('View Order'),
                'width'     => '70px',
                'type'      => 'action',
                'align'     =>'left',
                'getter'    => 'getOrderId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('advancedreports')->__('View'),
                        'url'     => array(
                            'base'=>'adminhtml/sales_order/view',
                            'params'=>array()
                        ),
                        'field'   => 'order_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));
		
        $this->addColumn('view_product',
            array(
                'header'    => Mage::helper('advancedreports')->__('View Product'),
                'width'     => '70px',
                'type'      => 'action',
                'align'     =>'left',
                'getter'    => 'getProductId',
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
        return 'none';
    }

    public function hasRecords()
    {
		return false;
    }

    public function getPeriods()
    {
        return array();
    }		
	
	public function getGridUrl()
	{
		$params = Mage::app()->getRequest()->getParams();
		$params['_secure'] = Mage::app()->getStore(true)->isCurrentlySecure();		
		return $this->getUrl('*/*/grid', $params);
	}		

}
