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
 */
/**
 * Advanced Grid Abstract Class
 */
class AW_Advancedreports_Block_Advanced_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    /**
     * Array of custom data that using to build chart
     * @var array
     */
    protected $_customData = array();

    /**
     * Array of Custom Data converted. Each element is Varien_Object
     * @var array
     */
    protected $_customVarData;

    /**
     * Differensies in Timezones
     * @var
     */
    protected $_timeDiff;

    /**
     * Current timezone
     * @var string
     */
    protected $_ctz;

    /**
     * Saved Grand Totals
     * @var array
     */
	protected $_grandTotals;

    /**
     * Key to sort Custom Data
     * @var string
     */
	protected $_sortBy = null;

    /**
     * Table names cache
     * @var array
     */
    protected $_tables = array();

    /**
     * Set up timezone
     */
    protected function _setTimezone()
    {
        $this->_ctz = date_default_timezone_get();
        $mtz =  Mage::app()->getStore()->getConfig('general/locale/timezone');
        @date_default_timezone_set( $mtz );
    }

    /**
     * Unset timezone
     */
    protected function _unsetTimezone()
    {
        @date_default_timezone_set( $this->_ctz );
    }

    /**
     * Retrives flag to reload grid after setting up of filter
     * @return boolean
     */
    public function getNeedReload()
    {
        return Mage::helper('advancedreports')->getNeedReload( $this->_routeOption );
    }

    /**
     * Retrives name of table in DB
     * @param string $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        if (!isset($this->_tables[$tableName])) {
            $this->_tables[$tableName] = Mage::getSingleton('core/resource')->getTableName($tableName);
        }
        return $this->_tables[$tableName];
    }

    /**
     * Retrives flag to calculate
     * @return boolean
     */
    public function getNeedTotal()
    {
        return Mage::helper('advancedreports')->getNeedTotal( $this->_routeOption );
    }

    /**
     * Retrives current report option
     * @return string
     */
    public function getRoute()
    {
        return $this->_routeOption;
    }

    /**
     * Retrives differencies between timezones in hours
     * (not use this one)
     * @return int
     */
    public function getTimeDiff()
    {
        if (!$this->_timeDiff)
        {
            $this->_timeDiff = Mage::helper('advancedreports')->getTimeDiff();
        }
        return $this->_timeDiff;
    }

    /**
     * Set up column filters
     */
	protected function _setColumnFilters()
	{
		if ( count( $this->getColumns() ) ){
			foreach ( $this->getColumns() as $column ){
				if ( $filter = $this->getFilter($column->getId()) ){
					$column->getFilter()->setValue($filter);
				}
			}
		}
	}

	/**
     * Retrives formatted datetime
     * Implements standart strptime() for crossplatformed use
     * @param Datetime $sDate
     * @param string $sFormat
     * @return string
     */
	protected function _strptime($sDate, $sFormat)
	{
        $aResult = array
        (
            'tm_sec'   => 0,
            'tm_min'   => 0,
            'tm_hour'  => 0,
            'tm_mday'  => 1,
            'tm_mon'   => 0,
            'tm_year'  => 0,
            'tm_wday'  => 0,
            'tm_yday'  => 0,
            'unparsed' => $sDate,
        );

        while($sFormat != "")
        {
            // ===== Search a %x element, Check the static string before the %x =====
            $nIdxFound = strpos($sFormat, '%');
            if($nIdxFound === false)
            {

                // There is no more format. Check the last static string.
                $aResult['unparsed'] = ($sFormat == $sDate) ? "" : $sDate;
                break;
            }

            $sFormatBefore = substr($sFormat, 0, $nIdxFound);
            $sDateBefore   = substr($sDate,   0, $nIdxFound);

            if($sFormatBefore != $sDateBefore) break;

            // ===== Read the value of the %x found =====
            $sFormat = substr($sFormat, $nIdxFound);
            $sDate   = substr($sDate,   $nIdxFound);

            $aResult['unparsed'] = $sDate;

            $sFormatCurrent = substr($sFormat, 0, 2);
            $sFormatAfter   = substr($sFormat, 2);

            $nValue = -1;
            $sDateAfter = "";

            switch($sFormatCurrent)
            {
                case '%S': // Seconds after the minute (0-59)

                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

                    if(($nValue < 0) || ($nValue > 59)) return false;

                    $aResult['tm_sec']  = $nValue;
                    break;

                // ----------
                case '%M': // Minutes after the hour (0-59)
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

                    if(($nValue < 0) || ($nValue > 59)) return false;

                    $aResult['tm_min']  = $nValue;
                    break;

                // ----------
                case '%H': // Hour since midnight (0-23)
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

                    if(($nValue < 0) || ($nValue > 23)) return false;

                    $aResult['tm_hour']  = $nValue;
                    break;

                // ----------
				case '%e':
                case '%d': // Day of the month (1-31)
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

                    if(($nValue < 1) || ($nValue > 31)) return false;

                    $aResult['tm_mday']  = $nValue;
                    break;

                // ----------
                case '%m': // Months since January (0-11)
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

                    if(($nValue < 1) || ($nValue > 12)) return false;

                    $aResult['tm_mon']  = ($nValue - 1);
                    break;

                // ----------
                case '%y': // Years since 1900
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

                    if($nValue >= 69 && $nValue <= 99){
                    	$aResult['tm_year']  = $nValue;
                    } else {
                    	$aResult['tm_year']  = ($nValue + 100);
                    }
                    break;

                // ----------
                case '%Y': // Years since 1900
                    sscanf($sDate, "%4d%[^\\n]", $nValue, $sDateAfter);

                    if($nValue < 1900) return false;

                    $aResult['tm_year']  = ($nValue - 1900);
                    break;

                // ----------
                default:
                    break 2; // Break Switch and while

            } // END of case format

            // ===== Next please =====
            $sFormat = $sFormatAfter;
            $sDate   = $sDateAfter;

            $aResult['unparsed'] = $sDate;

        } // END of while($sFormat != "")

        // ===== Create the other value of the result array =====
        $nParsedDateTimestamp = mktime($aResult['tm_hour'], $aResult['tm_min'], $aResult['tm_sec'],
                                $aResult['tm_mon'] + 1, $aResult['tm_mday'], $aResult['tm_year'] + 1900);

        // Before PHP 5.1 return -1 when error
        if(($nParsedDateTimestamp === false)
        ||($nParsedDateTimestamp === -1)) return false;

        $aResult['tm_wday'] = (int) strftime("%w", $nParsedDateTimestamp); // Days since Sunday (0-6)
        $aResult['tm_yday'] = (strftime("%j", $nParsedDateTimestamp) - 1); // Days since January 1 (0-365)

        return $aResult;
	}

    /**
     * Retrives filter semafor for Row of Data
     * @param Varien_Object $row
     * @return boolean
     */
	protected function _filterPass($row)
	{
		$result = true;
		if ($this->getFilterVisibility()){
			if (count($this->getColumns()))	{
				foreach ($this->getColumns() as $column){
					if ( ($filter = $this->getFilter($column->getId())) && (isset($row[$column->getId()])) ){
						if ($column->getType() == 'text' || $column->getType() == 'country'){
							# Filter by string
							if ($filter && $filter != ""){
								if (strpos(strtolower($row[$column->getId()]), strtolower($this->getFilter($column->getId()))) === false){
									$result = false;
								}
							}
						} elseif($column->getType() == 'datetime' || $column->getType() == 'date') {
							# Filter by Datetime
							if ($filter && is_array($filter) ){
								if ( isset( $row[$column->getId()] ) ){
									$val = $row[$column->getId()];
									if ( isset( $filter['from'] ) ){
										$date = new Zend_Date($filter['from'], Zend_Date::DATE_SHORT, $filter['locale']);
										if ($val <= date('Y-m-d 00:00:00', $date->getTimestamp())){
											$result = false;
										}
									}
									if ( isset( $filter['to'] ) ){
										$date = new Zend_Date($filter['to'], Zend_Date::DATE_SHORT, $filter['locale']);
										if ($val >= date('Y-m-d 23:59:59', $date->getTimestamp())){
											$result = false;
										}
									}
								}
							}
						} elseif($column->getType() == 'number' || $column->getType() == 'currency') {
							# filter by Number and Currency
							if ($filter && is_array($filter)){
								if ( isset( $row[$column->getId()] ) ){
									$val = $row[$column->getId()];
									if ( isset( $filter['from'] ) ){
										if ($val < $filter['from']){
											$result = false;
										}
									}
									if ( isset( $filter['to'] ) ){
										if ($val > $filter['to']){
											$result = false;
										}
									}
								}
							}
						} elseif($column->getType() == 'store' || $column->getType() == 'select') {
							# filter by Number and Currency
							if ($filter && $filter != ""){
								if ( isset( $row[$column->getId()] ) ){
									$val = $row[$column->getId()];
									if ( !is_array($val) ){
										$val = explode(",", $val);
									}
									if ( !in_array($filter, $val) ){
										$result = false;
									}
								}
							}
						}
					}
				}
			}
		}
		return $result;
	}

    /**
     * Retrives refresh button html
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
								->setLabel(Mage::helper('reports')->__('Reset Filter'))
								->setType('bullton')
								->setOnClick($this->getJsObjectName().'.resetFilter();')
								->toHtml();
    }

    /**
     * Format datetime from string to Mysql format
     * @param string $date
     * @return Datetime
     */
    protected function _getMysqlFromFormat($date)
    {
        $format = $this->getLocale()->getDateStrFormat( Mage_Core_Model_Locale::FORMAT_TYPE_SHORT );
        $arr = $this->_strptime($date, $format);
        $tme = mktime( 0,0,0,$arr['tm_mon']+1,$arr['tm_mday'],$arr['tm_year'] + 1900 );
        return date('Y-m-d 00:00:00', $tme );
    }

    /**
     * Format datetime from string to Mysql format
     * @param string $date
     * @return Datetime
     */
    protected function _getMysqlToFormat($date)
    {
        $format = $this->getLocale()->getDateStrFormat( Mage_Core_Model_Locale::FORMAT_TYPE_SHORT );
        $arr = $this->_strptime($date, $format);
        $tme = mktime( 23,59,59,$arr['tm_mon']+1,$arr['tm_mday'],$arr['tm_year'] + 1900 );
        return date('Y-m-d 23:59:59', $tme );
    }

    /**
     * Set up date filter to collection of grid
     * @param Datetime $from
     * @param Datetime $to
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    public function setDateFilter($from, $to)
    {
        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $this->getCollection()->getSelect()
                                ->where("main_table.created_at >= ?", $from)
                                ->where("main_table.created_at <= ?", $to);
        } else {
            $this->getCollection()->getSelect()
                                ->where("e.created_at >= ?", $from)
                                ->where("e.created_at <= ?", $to);
        }
		return $this;
    }

    /**
     * Set up store ids to filter collection
     * @param int|array $storeIds
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    public function setStoreFilter($storeIds)
    {
        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $this->getCollection()->getSelect()
                    ->where("main_table.store_id in ('".implode("','", $storeIds)."')");
        } else {
            $this->getCollection()->getSelect()
                    ->where("e.store_id in ('".implode("','", $storeIds)."')");
        }
		return $this;
    }

    /**
     * Set up order by total
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    public function orderByTotal()
    {
		$this->getCollection()->getSelect()
				->order('sum_total DESC');
		return $this;
    }

    /**
     * Set up order by quantitty
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    public function orderByQty()
    {
		$this->getCollection()->getSelect()
				->order('sum_qty DESC');

		return $this;
    }

    /**
     * Set up Hour filter
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    public function setHourFilter()
    {
		$itemTable = $this->getTable('sales_flat_order_item');

        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $this->getCollection()->getSelect()
                    ->join( array('item'=>$itemTable), "main_table.entity_id = item.order_id AND item.parent_item_id IS NULL", array( 'hour' => 'HOUR( main_table.created_at )', 'sum_qty' => 'SUM(item.qty_ordered)',  'sum_total' => 'SUM(item.base_row_total)', 'name' => 'name', 'sku'=>'sku' ) )
                    ->group('hour');
        } else {
            $this->getCollection()->getSelect()
                    ->join( array('item'=>$itemTable), "e.entity_id = item.order_id AND item.parent_item_id IS NULL", array( 'hour' => 'HOUR( e.created_at )', 'sum_qty' => 'SUM(item.qty_ordered)',  'sum_total' => 'SUM(item.base_row_total)', 'name' => 'name', 'sku'=>'sku' ) )
                    ->group('hour');
        }
		return $this;
    }

    /**
     * Set up day of week filter
     * $dayofweek = 1,2,3,4,5,6,7
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    public function setDayOfWeekFilter()
    {
		$itemTable = $this->getTable('sales_flat_order_item');

        if (Mage::helper('advancedreports')->checkVersion('1.4.1.0')){
            $this->getCollection()->getSelect()
                    ->join( array('item'=>$itemTable), "main_table.entity_id = item.order_id AND item.parent_item_id IS NULL", array( 'day_of_week' => 'DAYOFWEEK(main_table.created_at)', 'sum_qty' => 'SUM(item.qty_ordered)',  'sum_total' => 'SUM(item.base_row_total)', 'name' => 'name', 'sku'=>'sku' ) )
                    ->group('day_of_week');
        } else {
            $this->getCollection()->getSelect()
                    ->join( array('item'=>$itemTable), "e.entity_id = item.order_id AND item.parent_item_id IS NULL", array( 'day_of_week' => 'DAYOFWEEK(e.created_at)', 'sum_qty' => 'SUM(item.qty_ordered)',  'sum_total' => 'SUM(item.base_row_total)', 'name' => 'name', 'sku'=>'sku' ) )
                    ->group('day_of_week');
        }        
		return $this;
    }

    /**
     * Retrives filter string
     * @return string
     */
    protected function _getProcessStates()
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
		    $filter .= "val.value = '".$state."'";
		    $is_first = false;
		}
		return "(".$filter.")";
    }

    /**
     * Set up order state filter
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
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

    /**
     * Prepare collection to use
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    protected  function _prepareCollection()
    {
        parent::_prepareCollection();

		$this->setCollection( Mage::getModel('sales/order')->getCollection() );
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
        //$this->_prepareData();
        return $this;
    }

    /**
     * Prepare collection to use by older method of prepare
     * @return AW_Advancedreports_Block_Advanced_Grid
     */
    protected function _prepareOlderCollection()
    {
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Retrives ids separated by comma
     * @param array $ids
     * @return string
     */
    public function getStoreIds($ids = array())
    {
        if (count($ids))
        {
            $res = '';
            $is_first = true;
            foreach ($ids as $id)
            {
                $res .= $is_first ? $id : ",".$id;
                $is_first = false;
            }
        }
        return $res;
    }

    /**
     * Retrives chart params to build line chart
     * @return array
     */
    public function getChartParams()
    {
        return Mage::helper('advancedreports')->getChartParams( $this->_routeOption );
    }

    /**
     * Retrives flag to show chart
     * @return boolean
     */
    public function hasRecords()
    {
        return (count( $this->_customData ) > 1)
               && Mage::helper('advancedreports')->getChartParams( $this->_routeOption )
               && count( Mage::helper('advancedreports')->getChartParams( $this->_routeOption ) );
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

    /**
     * Set up sort direction
     * @param string $id
     * @param string $dir
     */
	protected function _setColumnDir($id, $dir)
	{
		if (count($this->_columns)){
			foreach ($this->_columns as $column){
				if ($column->getId() == $id){
					$column->setDir($dir);
					$this->_sortBy = $id;
					return;
				}
			}
		}
	}

    /**
     * Retrives sort key
     * @return string
     */
	protected function _getSort()
	{
		return $this->getRequest()->getParam('sort');
	}

    /**
     * Retrives sort direction
     * @return string
     */
	protected function _getDir()
	{
		return $this->getRequest()->getParam('dir');
	}

    /**
     * Prepare data to build grid
     */
	protected function _prepareData()
	{
		if ($this->_getDir()){
			$this->_setColumnDir($this->_getSort(), $this->_getDir());
		}
	}

	protected function _compareVarDataElements($a, $b)
	{
		$key = "get".Mage::helper('advancedreports')->getDataKey($this->_getSort());
        if ($a->$key() == $b->$key())
        {
            return 0;
        }
		if ($this->_getDir() == "asc"){
			return ($a->$key() < $b->$key()) ? -1 : 1;
		} else {
			return ($a->$key() > $b->$key()) ? -1 : 1;
		}
	}

    /**
     * Retrives Custom Data array with Varien_Object converted data rows
     * @return array
     */
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
		if ($this->_customVarData && is_array($this->_customVarData) && $this->_getSort() && $this->_getDir()){
			usort($this->_customVarData, array(&$this, "_compareVarDataElements") );
		}
        return $this->_customVarData;
    }

    /**
     * Retrives empty Periods array
     * @return array
     */
    public function getPeriods()
    {
        return array();
    }

    /**
     * Retrives old version of getPeriods()
     * @return array
     */
    protected function _getOlderPeriods()
    {
        return parent::getPeriods();
    }

    /**
     * Retrives Excel file content
     * @param string $filename
     * @return string
     */
    public function getOldExcel($filename = '')
    {
        return parent::getExcel($filename);
    }

    /**
     * Retrives CSV file content
     * @return string
     */
    public function getOldCsv()
    {
        return parent::getCsv();
    }

    /**
     * Retrives Grand Totals array
     * @return array
     */
	public function getGrandTotals()
	{
		if (!$this->_grandTotals){
			$this->_grandTotals = new Varien_Object();
			if (count($this->_customData)){
				foreach ($this->_columns as $column){
					if (($column->getType() == "currency" || $column->getType() == "number") && !$column->getDisableTotal()){
						$sum = 0;
						foreach ($this->_customData as $data){
							if (isset($data[$column->getIndex()])){
								$sum += $data[$column->getIndex()];
							}
						}
						$this->_grandTotals->setData($column->getIndex(), $sum);
					}
				}
			}
		}
		return $this->_grandTotals;
	}

    /**
     * Retrives count of totals
     * @return int
     */
	public function getCountTotals()
	{
		$count = 0;
		foreach ($this->_columns as $column){
			if (($column->getType() == "currency" || $column->getType() == "number") && !$column->getDisableTotal()){
				$count ++;
			}
		}
		return $count;
	}

    /**
     * Retrives Excel file content
     * @param string $filename
     * @return string
     */
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
            if ($this->getNeedTotal() && $this->getCountTotals() && count( $this->getCustomVarData() ))
            {
                $_isFirst = true;
                $row = array();
                foreach ($this->_columns as $_column){
                    if ($_isFirst){
                        $row[] = $this->getTotalText();
                    } elseif ($_column->getType() == "action" || $_column->getDisableTotal()) {
                        $row[] = "";
                    } else {
                        $row[] = $_column->getRowField($this->getGrandTotals());
                    }
                    $_isFirst = false;
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

    /**
     * Retrives CSV file content
     * @return string
     */
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

		if ($this->getNeedTotal() && $this->getCountTotals() && count( $this->getCustomVarData() ))
		{
			$_isFirst = true;
			$data = array();
			foreach ($this->_columns as $_column){
				if ($_isFirst){
					$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $this->getTotalText()).'"';
				} elseif ($_column->getType() == "action" || $_column->getDisableTotal()) {
					$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), "").'"';
				} else {
					$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $_column->getRowField($this->getGrandTotals())).'"';
				}
				$_isFirst = false;
			}
			$csv.= implode(',', $data)."\n";
		}

        return $csv;
    }
}
