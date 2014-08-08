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
class AW_Advancedreports_Helper_Data extends Mage_Core_Helper_Abstract
{
    const GRID_TEMPLATE = 'advancedreports/grid.phtml';

    const ROUTE_SALES_SALES = 'sales_sales';
    const ROUTE_ADVANCED_BESTSELLERS = 'advanced_bestsellers';
    const ROUTE_ADVANCED_COUNTRY = 'advanced_country';
    const ROUTE_ADVANCED_HOURS = 'advanced_hours';
    const ROUTE_ADVANCED_DAYOFWEEK = 'advanced_dayofweek';
    const ROUTE_ADVANCED_PRODUCTS = 'advanced_products';
    const ROUTE_ADVANCED_USERS = 'advanced_users';
    const ROUTE_ADVANCED_SALES = 'advanced_sales';
    const ROUTE_ADVANCED_USERGROUPS = 'advanced_usergroups';
	const ROUTE_ADVANCED_PURCHASED = 'advanced_purchased';

    protected $_weekdays;
    protected $_locale;

    public function getGridTemplate()
    {
        return self::GRID_TEMPLATE;
    }

    /*
     * It's return difference between MysqlTimeZone and Magento TimeZone
     * 
     */
    public function getTimeDiff()
    {
        $diff = 0;
        //$ctz = date_default_timezone_get();
        //$mtz =  Mage::app()->getStore()->getConfig('general/locale/timezone');
        //@date_default_timezone_set( $mtz );
        //$magtime = strtotime( gmdate( 'Y-m-d H:i', time() ) );
        //@date_default_timezone_set( $ctz );
        //$mysqltime = time();
        //$diff = round( ($mysqltime - $magtime) / 3600 );
        return $diff;
    }
	
	/*
	 * Returns AW_Advancedreports version
	 */
	public function getVersion()
	{
		return Mage::getConfig()->getNode('modules/AW_Advancedreports/version');
	}
	
	/*
	 * Compare param $version with magento version
	 */
	public function checkVersion($version)
	{
		return version_compare(Mage::getVersion(), $version, '>=');
	}	
	
    protected function _getLastWeekLabel()
    {
		$firstDayNum = Mage::getStoreConfig('general/locale/firstday')?Mage::getStoreConfig('general/locale/firstday'):0;
		$lastDayNum = $firstDayNum + 6;
		$lastDayNum = $lastDayNum > 6 ? $lastDayNum - 7 : $lastDayNum;
		return $this->__('Last week').' ('.substr($this->getWeekday($firstDayNum), 0,3).' - '.substr($this->getWeekday($lastDayNum),0,3 ).')';
    }
    
    protected function _getFirstWeekDay()
    {
		return Mage::getStoreConfig('general/locale/firstday')?Mage::getStoreConfig('general/locale/firstday'):0;
    }

    protected function _getLastWeekDay()
    {
		$firstDayNum = Mage::getStoreConfig('general/locale/firstday')?Mage::getStoreConfig('general/locale/firstday'):0;
		$lastDayNum = $firstDayNum + 6;
		return $lastDayNum > 6 ? $lastDayNum - 7 : $lastDayNum;
    }

    protected function _getWeekDayName($index)
    {
		$days = array(
		    0 => 'sun',
		    1 => 'mon',
		    2 => 'tue',
		    3 => 'wed',
		    4 => 'thu',
		    5 => 'fri',
		    6 => 'sat',
		);	
		return isset($days[$index])? $days[$index] : null;
    }

    public function getOptions()
    {
        return array(
            array( 'value'=>'today', 'label'=> $this->__('Today') ),
            array( 'value'=>'yesterday', 'label'=> $this->__('Yesterday') ),
            array( 'value'=>'last_7_days', 'label'=> $this->__('Last 7 days') ),
            array( 'value'=>'last_week', 'label'=>$this->_getLastWeekLabel()  ),
            array( 'value'=>'last_business_week', 'label'=> $this->__('Last business week (Mon - Fri)') ),
            array( 'value'=>'this_month', 'label'=> $this->__('This month'), 'default' => 1 ),
            array( 'value'=>'last_month', 'label'=> $this->__('Last month') ),
            array( 'value'=>'custom', 'label'=> $this->__('Custom date range') ),
        );
    }

    public function getRangeValues()
    {
        $ctz = date_default_timezone_get();
        $mtz =  Mage::app()->getStore()->getConfig('general/locale/timezone');
        @date_default_timezone_set( $mtz );
	
		$firstDay = $this->_getWeekDayName( $this->_getFirstWeekDay() );
		$lastDay = $this->_getWeekDayName( $this->_getLastWeekDay() );
	
        $format = $this->getDateFormat();
        $res = array(
            array(
               'key'  => 'today',
               'from' => strftime( $format ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'yesterday',
               'from' => strftime( $format, strtotime('yesterday') ),
               'to'   => strftime( $format, strtotime('yesterday') )
            ),
            array(
               'key'  => 'last_7_days',
               'from' => strftime( $format, strtotime('- 7 days') ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'last_week',
               'from' => strftime( $format, strtotime( $firstDay ) ) === strftime( $format, strtotime( 'today' ) ) ? strftime( $format, strtotime( 'last '.$firstDay ) ) : strftime( $format, strtotime('last week '.$firstDay.' - 7 days') ),
               'to'   => strftime( $format, strtotime('last week '.$lastDay ) )
            ),
            array(
               'key'  => 'last_business_week',
               'from' => strftime( $format, strtotime( 'monday' ) ) === strftime( $format, strtotime( 'today' ) ) ? strftime( $format, strtotime( 'last monday' ) ) : strftime( $format, strtotime('last week mon - 7 days') ),
               'to'   => strftime( $format, strtotime('last week fri') )
            ),
            array(
               'key'  => 'this_month',
               'from' => strftime( $format, strtotime( date('m/01/y') ) ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'last_month',
               'from' => strftime( $format, strtotime( date('m/01/y', strtotime( 'last month' ) ) ) ),
               'to'   => strftime( $format, strtotime( date('m/01/y').' - 1 day' ) )
            ),
        );
        @date_default_timezone_set( $ctz );
        return $res;
    }

    public function getDateFormat()
    {
        return $this->getLocale()->getDateStrFormat( Mage_Core_Model_Locale::FORMAT_TYPE_SHORT );
    }

    public function getLocale()
    {
        if ( !$this->_locale )
        {
            $this->_locale = Mage::app()->getLocale();
        }
        return $this->_locale;
    }

    # start Conf part
    public function confProcessOrders()
    {
	$res = Mage::getStoreConfig('advancedreports/configuration/process_orders');
        return $res ? $res : Mage_Sales_Model_Order::STATE_COMPLETE;
    }

    public function confShowChart()
    {
        return Mage::getStoreConfig('advancedreports/chart_options/show_chart');
    }

    public function getChartHeight()
    {
        return Mage::getStoreConfig('advancedreports/chart_options/height');
    }

    public function getChartColor()
    {
        return Mage::getStoreConfig('advancedreports/chart_options/chart_color');
    }

    public function getChartFontColor()
    {
        return Mage::getStoreConfig('advancedreports/chart_options/font_color');
    }

    public function getChartFontSize()
    {
        return Mage::getStoreConfig('advancedreports/chart_options/font_size');
    }

    public function getChartBackgroundColor()
    {
        return Mage::getStoreConfig('advancedreports/chart_options/background_color');
    }

    public function confBestsellersCount()
    {
        return Mage::getStoreConfig('advancedreports/bestsellers_options/bestsellers_count');
    }
    # end Conf part
   
    public function getChartParams($key)
    {
        $params = array();
        $params[self::ROUTE_SALES_SALES] = array(
                    array( 'value'=>'total', 'label'=>'Total' ),
                    array( 'value'=>'subtotal', 'label'=>'Subtotal' ),
                    array( 'value'=>'orders', 'label'=>'Number of Orders' ),
                    array( 'value'=>'items', 'label'=>'Items Ordered' ),
               );

        $params[self::ROUTE_ADVANCED_BESTSELLERS] = array(
                    array( 'value'=>'percent_data', 'label'=>'Percent' ),
               );

        $params[self::ROUTE_ADVANCED_COUNTRY] = array(
                    array( 'value'=>'percent_data', 'label'=>'Percent' ),
               );

        $params[self::ROUTE_ADVANCED_HOURS] = array(
                    array( 'value'=>'data_for_chart', 'label'=>'Data for Chart' ),
               );

        $params[self::ROUTE_ADVANCED_DAYOFWEEK] = array(
                    array( 'value'=>'data_for_chart', 'label'=>'Quantity' ),
               );

        $params[self::ROUTE_ADVANCED_PRODUCTS] = array(
                    array( 'value'=>'ordered_qty', 'label'=>'Quantity' ),
               );

        $params[self::ROUTE_ADVANCED_USERS] = array(
                    array( 'value'=>'orders', 'label'=>'Orders' ),
               );
	       
        $params[self::ROUTE_ADVANCED_SALES] = array(
                    array( 'value'=>'ordered_qty', 'label'=>'Quantity' ),
               );

        $params[self::ROUTE_ADVANCED_USERGROUPS] = array(
                    array( 'value'=>'percent_data', 'label'=>'Percent' ),
               );
			   
        $params[self::ROUTE_ADVANCED_PURCHASED] = array(
                    array( 'value'=>'customers', 'label'=>'Quantity' ),
               );			   

        return $params[$key];
    }

    public function getDataKey($key)
    {
		if ($key){
			$in = explode("_", $key);
			$out = array();
			foreach ($in as $el){
				$out[] = ucwords(strtolower($el));
			}
			return implode("", $out);						
		}
		return 'NullKey';
    }
    
    public function getNeedReload($key)
    {
        $params = array();
        $params[self::ROUTE_SALES_SALES] = false;
        $params[self::ROUTE_ADVANCED_BESTSELLERS] = true;
        $params[self::ROUTE_ADVANCED_COUNTRY] = true;
        $params[self::ROUTE_ADVANCED_HOURS] = true;
        $params[self::ROUTE_ADVANCED_DAYOFWEEK] = true;
        $params[self::ROUTE_ADVANCED_PRODUCTS] = true;
        $params[self::ROUTE_ADVANCED_USERS] = false;
        $params[self::ROUTE_ADVANCED_SALES] = false;
        $params[self::ROUTE_ADVANCED_USERGROUPS] = true;
		$params[self::ROUTE_ADVANCED_PURCHASED] = false;
        return $params[$key];
    }
	
	public function getNeedTotal($key)
	{
        $params = array();
        $params[self::ROUTE_SALES_SALES] = true;
        $params[self::ROUTE_ADVANCED_BESTSELLERS] = true;
        $params[self::ROUTE_ADVANCED_COUNTRY] = true;
        $params[self::ROUTE_ADVANCED_HOURS] = true;
        $params[self::ROUTE_ADVANCED_DAYOFWEEK] = true;
        $params[self::ROUTE_ADVANCED_PRODUCTS] = true;
        $params[self::ROUTE_ADVANCED_USERS] = true;
        $params[self::ROUTE_ADVANCED_SALES] = true;
        $params[self::ROUTE_ADVANCED_USERGROUPS] = true;
		$params[self::ROUTE_ADVANCED_PURCHASED] = true;
        return $params[$key];		
	}

    public function getReloadKeys()
    {
        return array(
                    array('value'=>'qty','label'=>'Quantity'),
                    array('value'=>'total','label'=>'Total'),
                    );
    }

    public function setChartKeys( $data, $key = 'Chart' )
    {
        $session = Mage::getSingleton('core/session', array('name'=>'backend'))->start();
		$route = 'setAwChartKeys'.$key;
        $session->$route( $data );
        return $this;
    }

    public function getChartKeys($key = 'Chart')
    {
        $session = Mage::getSingleton('core/session', array('name'=>'backend'))->start();
		$route = 'getAwChartKeys'.$key;
        return $session->$route();
    }

    public function setChartData( $data, $key = 'Chart' )
    {
        $session = Mage::getSingleton('core/session', array('name'=>'backend'))->start();
        $route = 'setAwChartData'.$key;
        $session->$route( $data );
        return $this;
    }

    public function getChartData( $key = 'Chart')
    {
        $session = Mage::getSingleton('core/session', array('name'=>'backend'))->start();
        $route = 'getAwChartData'.$key;
        return $session->$route();
    }


    public function setChartLabels( $data, $key = 'Chart' )
    {
        $session = Mage::getSingleton('core/session', array('name'=>'backend'))->start();
		$route = 'setAwChartLabels'.$key;
        $session->$route( $data );
        return $this;
    }

    public function getChartLabels( $key = 'Chart' )
    {
        $session = Mage::getSingleton('core/session', array('name'=>'backend'))->start();
		$route = 'getAwChartLabels'.$key;
        return $session->$route();
    }

    public function setSkus( $data )
    {
        $session = Mage::getSingleton('core/session', array('name'=>'backend'))->start();
        $session->setAwSkus( $data );
        return $this;
    }

    public function getSkus()
    {
        $session = Mage::getSingleton('core/session', array('name'=>'backend'))->start();
        return $session->getAwSkus();
    }

    public function getProductSkuBySku($sku)
    {
        if ( $product_id = Mage::getModel('catalog/product')->getIdBySku($sku) )
        {
            return Mage::getModel('catalog/product')->load($product_id)->getSku();
        }
		elseif ($product_id = Mage::getModel('catalog/product')->getIdBySku( $this->getDisoptedSku($sku) ))
		{
			return $sku;
		}		
        return '';
    }
	
	/*
	 * Intelegent SKU extraction
	 */
	public function getDisoptedSku($sku)
	{
		$arr = explode('-', $sku);	
		while ( count($arr) > 1 && !($product_id = Mage::getModel('catalog/product')->getIdBySku(implode('-', $arr)) ) ){
			unset( $arr[count($arr) - 1] );
		}
		return implode('-', $arr);		
	}

    public function getProductNameBySku($sku)
    {
        if ( $product_id = Mage::getModel('catalog/product')->getIdBySku($sku) )
        {
            return Mage::getModel('catalog/product')->load($product_id)->getName();
        }
		elseif ($product_id = Mage::getModel('catalog/product')->getIdBySku( $this->getDisoptedSku($sku) ))
		{
			return Mage::getModel('catalog/product')->load($product_id)->getName()." ({$sku})" ;
		}
        return '';
    }

    public function getWeekday($weekday)
    {
        if (!$this->_weekdays)
        {
            $this->_weekdays = Mage::app()->getLocale()->getOptionWeekdays();
        }
        foreach ($this->_weekdays as $day)
        {
            if ($day['value'] == $weekday)
            {
                return $day['label'];
            }
        }
    }

}

