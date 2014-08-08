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
class AW_Advancedreports_Block_Chart extends Mage_Adminhtml_Block_Template
{
    const CHART_TEMPLATE = 'advancedreports/chart.phtml';
    const API_URL = 'http://chart.apis.google.com/chart';
    const TEST_HOST = 'chart.apis.google.com';

    const CHART_TYPE_PIE3D = 'p3';
    const CHART_TYPE_LINE  = 'lc';
    const CHART_TYPE_MULTY_LINE  = 'mul_lc';
    const CHART_TYPE_MAP    = 't';
    const CHART_TYPE_BARS   = 'bvs';
    
    protected $_height = '220';
    protected $_width = '1000';
    protected $_values = array();
    protected $_keys = array();
    protected $_labels = array();
    protected $_routeOption;
    protected $_option;
    protected $_type;
    protected $_multyLineColors = array(
            '009244','80B63E','F6EB2C','E86F2D',
            'EB3A05','E01D33','DF086D','88167B',
            '0097D9','0097D9','000000','404040','808080');

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate( self::CHART_TEMPLATE );
    }

    public function setType( $value )
    {
        $this->_type = $value;
        return $this;
    }

    public function setRouteOption( $value )
    {
        $this->_routeOption = $value;
        return $this;
    }

    public function setOption( $value )
    {
        $this->_option = $value;
        return $this;
    }

    public function getOption()
    {        
        return $this->_option;
    }

    public function getHeight()
    {        
        return $this->_height;
    }

    public function getWidth()
    {
        return $this->_width;
    }

    public function setHeight( $value )
    {    
        $this->_height = ($value - 40) > 300 ? 300 : $value;
        return $this;
    }

    public function setWidth( $value )
    {
        $this->_width = ($value - 40) > 1000 ? 1000 : $value - 40;
//        echo $this->getChartUrl();
        return $this;
    }

    public function canShow()
    {
        $test = $this->_testUrl( $this->getChartUrl() );
        return $test === '200' || $test === '0';
    }
    
    protected function _getLabelByKey($key)
    {
		if (isset($this->_labels[$key]))
		{
		    return $this->_labels[$key];
		}
    }

    protected function _getDataFromSession()
    {
        if (!count($this->_values))
        {
            $this->_values = Mage::helper('advancedreports')->getChartData( Mage::helper('advancedreports')->getDataKey( $this->_routeOption ) );
            $this->_keys = Mage::helper('advancedreports')->getChartKeys( Mage::helper('advancedreports')->getDataKey( $this->_routeOption ) );
	    	$this->_labels = Mage::helper('advancedreports')->getChartLabels( Mage::helper('advancedreports')->getDataKey( $this->_routeOption ) );
        }            
        return $this;
    }


    /*
     * Its return HTTP code in if fsockopen is enabled
     * and return 0 if fsock is disabled
     */
    protected function _testUrl( $url )
    {
        $fp = @fsockopen(self::TEST_HOST, 80, $errno, $errstr, 30);
        if (!$fp)
        {
            return '0'; # it's mean OK
        }
        else
        {
            $url1 = str_replace('http://'.self::TEST_HOST, '', $url);
            $url1 = str_replace(' ', '%20', $url1);
            $out = "GET ".$url1." HTTP/1.1\r\n";
            $out .= "Host: ".self::TEST_HOST."\r\n";
            $out .= "Connection: Close\r\n\r\n";

            fwrite($fp, $out);
            $answer = '';
            while ( strlen($answer) < 12 )
            {
                $answer .= fgets($fp, 128);
            }
            fclose($fp);
            $arr = explode(' ', $answer);
            return isset($arr[1]) ? $arr[1] : '0';
        }       
    }

    protected function _getRandomColor()
    {
        mt_srand( (double)microtime()*1000000 );
        $c = '';
        while( strlen($c) < 6 )
        {
            $c .= sprintf("%02X", mt_rand(0, 255));
        }
        return $c;
    }

    public function getChartUrl($directUrl = true)
    {
        $this->_getDataFromSession();
        $s = '';
        if ( $this->_type === self::CHART_TYPE_LINE )
        {
            $s =  $this->getLineChartUrl();
        }
        elseif ( $this->_type === self::CHART_TYPE_PIE3D )
        {
            $s =  $this->getPieChartUrl();
        }
        elseif ( $this->_type === self::CHART_TYPE_MAP )
        {
            $s =  $this->getMapChartUrl();
        }
        elseif ( $this->_type === self::CHART_TYPE_BARS )
        {
            $s =  $this->getBarsChartUrl();
        }
        elseif ( $this->_type === self::CHART_TYPE_MULTY_LINE )
        {
            $s =  $this->getMultyLineChartUrl();
        }
        return $s;
    }

    public function getBarsChartUrl($directUrl = true)
    {
		# try to prevent foreach() errors
		if ( !$this->_values )
		{
		    return '';
		}

        $params = array(
            'cht'  => 'bvs',
            'chf'  => 'bg,s,'.Mage::helper('advancedreports')->getChartBackgroundColor(),
//            'chm'  => 'B,f4d4b2,0,0,0',
            'chco' => Mage::helper('advancedreports')->getChartColor(),
            'chs'  => $this->_width.'x'.$this->_height,
            'chbh' => 'a',
        );

        # Set up labels style
        $params['chxs'] = '0,'.Mage::helper('advancedreports')->getChartFontColor().','.Mage::helper('advancedreports')->getChartFontSize().'|1,'.Mage::helper('advancedreports')->getChartFontColor().','.Mage::helper('advancedreports')->getChartFontSize();
        
        # Getting data
        $values = array();
        $titles = array();
        $maxValue = 0;
        $minValue = 0;
        foreach ( $this->_values as $row )
        {
            $titles[] = $row['title'];
            $values[]  = isset($row[$this->_option]) ? $row[$this->_option] : 0 ;
            if ( isset($row[$this->_option]) && $row[$this->_option] > $maxValue )
            {
                $maxValue = $row[$this->_option];
            }
            if ( isset($row[$this->_option]) && $row[$this->_option] < $minValue )
            {
                $minValue = $row[$this->_option];
            }
        }

        /**
         * setting skip step
         */
        if (count($titles) > 8 && count($titles) < 15)
        {
            $c = 0;
        }
        else if (count($titles) >= 15)
        {
//            $c = ceil(count($titles) / 15);
            $c = 0;
        }
        else
        {
            $c = 0;
        }
        /**
         * skipping some x labels for good reading
         */
        $i=0;
        $xLabels = array();
        foreach ($titles as $k => $d)
        {
            if ($i == $c)
            {
                $xLabels[$k] = $d;
                $i = 0;
            }
            else
            {
                $xLabels[$k] = '';
                $i++;
            }
        }

        # Set up axis labels
        $params['chxt'] = 'x,y';

        # Set up x Labels
        $params['chxl'] = '0:|';
        $dataDelimiter = '|';
        $is_first = true;
        foreach ( $xLabels as $title )
        {
            if ( !$is_first )
            {
                $params['chxl'] .= $dataDelimiter;
            }
            $params['chxl'] .= $title;
            $is_first = false;
        }


        # Set up lines
        if ($minValue >= 0 && $maxValue >= 0)
        {
            $minY = 0;
            if ($maxValue > 10)
            {
                $p = pow(10, $this->_getPow($maxValue));
                $maxY = (ceil($maxValue/$p))*$p;
                $yLabels = range($minY, $maxY, $p);
            }
            else
            {
                $maxY = ceil($maxValue+1);
                $yLabels = range($minY, $maxY, 1);
            }
            $yrange = $maxY;
            $yorigin = 0;
        }

        $deltaX = 100 / (count($xLabels));
        if ( sizeof($yLabels) > 1 )
        {
            $deltaY = 100 /( sizeof($yLabels) - 1 );
        }
        else
        {
            $deltaY = 100;
        }

        $params['chg'] = $deltaX.','.$deltaY.',2,1';

        # Set up y axis
        $params['chxl'] .= '|1:|';
        $dataDelimiter = '|';
        $is_first = true;
        foreach ( $yLabels as $label )
        {
            if ( !$is_first )
            {
                $params['chxl'] .= $dataDelimiter;
            }
            $params['chxl'] .= $label;
            $is_first = false;
        }

        # Set up data
        $params['chd'] = 't:';
        $dataDelimiter = ',';
        $coof = $maxY / 100;
        $is_first = true;
        foreach ( $values as $value )
        {
            if ( !$is_first )
            {
                $params['chd'] .= $dataDelimiter;
            }
            $params['chd'] .= $coof != 0 ? round( $value / $coof ) : '0';
            $is_first = false;
        }

        # Return URL
        $url = self::API_URL;
        $is_first = true;
        foreach ($params as $key=>$param)
        {
            $url .= $is_first ? '?' : '&';
            $url .= $key.'='.$param;
            $is_first = false;
        }
        return $url;
    }    

    public function getMapChartUrl($directUrl = true)
    {
	# try to prevent foreach() errors
	if ( !$this->_values )
	{
	    return '';
	}

        $params = array(
            'cht'  => 't',
            'chs'  => ($this->_width <= 440 ? $this->_width : 440).'x220',   //$this->_height,
            'chco' => 'fcfcc2,FF0000,FFFF00,00FF00',
            'chtm' => 'world',
            'chf'  => 'bg,s,'.Mage::helper('advancedreports')->getChartBackgroundColor(),
        );

        # Getting data
        $countrys = array();
        $values = array();
        $maxValue = 0;
        $minValue = 0;
        foreach ( $this->_values as $row )
        {
            $countrys[] = $row['country_id'];
            $values[]  = isset($row[$this->_option]) ? $row[$this->_option] : 0 ;
            if ( isset($row[$this->_option]) && $row[$this->_option] > $maxValue )
            {
                $maxValue = $row[$this->_option];
            }
            if ( isset($row[$this->_option]) && $row[$this->_option] < $minValue )
            {
                $minValue = $row[$this->_option];
            }
        }

        # Set up data
        $params['chd'] = 't:';
        $dataDelimiter = ',';
        $coof = $maxValue / 100;
        $is_first = true;
        foreach ( $values as $value )
        {
            if ( !$is_first )
            {
                $params['chd'] .= $dataDelimiter;
            }
            $params['chd'] .= $coof != 0 ? round( $value / $coof ) : '0';
            $is_first = false;
        }

        # Set up countries
        $params['chld'] = '';
        $dataDelimiter = '';
        foreach ( $this->_values as $value )
        {
            $params['chld'] .= $value['country_id'];
        }

        # Return URL
        $url = self::API_URL;
        $is_first = true;
        foreach ($params as $key=>$param)
        {
            $url .= $is_first ? '?' : '&';
            $url .= $key.'='.$param;
            $is_first = false;
        }
        return $url;
    }
    
    public function getPieChartUrl($directUrl = true)
    {
	# try to prevent foreach() errors
	if ( !$this->_values )
	{
	    return '';
	}
	
        $params = array(
            'cht'  => 'p3',
            'chs'  => $this->_width.'x'.$this->_height,
            'chf'  => 'bg,s,'.Mage::helper('advancedreports')->getChartBackgroundColor(),
            'chco' => Mage::helper('advancedreports')->getChartColor(),
        );

        # Set up labels style
        $params['chxt'] = 'x';      
        $params['chxs'] = '0,'.Mage::helper('advancedreports')->getChartFontColor().','.Mage::helper('advancedreports')->getChartFontSize();

        foreach ( $this->_values as $row )
        {
            $titles[] = $row['title'];
            $values[]  = isset($row[$this->_option]) ? $row[$this->_option] : 0 ;
        }

        # Set up data
        $params['chl'] = '';
        $titleDelimiter = '|';
        $is_first = true;
        foreach ( $titles as $title )
        {
            if ( !$is_first )
            {
                $params['chl'] .= $titleDelimiter;
            }
            $params['chl'] .= str_replace('"',' ',str_replace('&',' ',$title));
            $is_first = false;
        }

        # Set up titles
        $params['chd'] = 't:';
        $dataDelimiter = ',';
        $is_first = true;
        foreach ( $values as $value )
        {
            if ( !$is_first )
            {
                $params['chd'] .= $dataDelimiter;
            }
            $params['chd'] .= $value;
            $is_first = false;
        }

        # Return URL
        $url = self::API_URL;
        $is_first = true;
        foreach ($params as $key=>$param)
        {
            $url .= $is_first ? '?' : '&';
            $url .= $key.'='.$param;
            $is_first = false;
        }
        return $url;
    }        

    public function getLineChartUrl($directUrl = true)
    {
	# try to prevent foreach() errors
	if ( !$this->_values )
	{
	    return '';
	}	

        $params = array(
            'cht'  => 'lc',
            'chf'  => 'bg,s,'.Mage::helper('advancedreports')->getChartBackgroundColor(),
//            'chm'  => 'B,f4d4b2,0,0,0', # it s background like in standard
            'chco' => Mage::helper('advancedreports')->getChartColor(),
            'chs'  => $this->_width.'x'.$this->_height, 
        );

        # Set up labels style
        $params['chxs'] = '0,'.Mage::helper('advancedreports')->getChartFontColor().','.Mage::helper('advancedreports')->getChartFontSize().'|1,'.Mage::helper('advancedreports')->getChartFontColor().','.Mage::helper('advancedreports')->getChartFontSize();

        # Getting data
        $values = array();
        $periods = array();
        $maxValue = 0;
        $minValue = 0;
        foreach ( $this->_values as $row )
        {
            $periods[] = $row['period'];
            $values[]  = isset($row[$this->_option]) ? $row[$this->_option] : 0 ; 
            if ( isset($row[$this->_option]) && $row[$this->_option] > $maxValue )
            {
                $maxValue = $row[$this->_option];
            }
            if ( isset($row[$this->_option]) && $row[$this->_option] < $minValue )
            {
                $minValue = $row[$this->_option];
            }
        }


	/*
	 * Get may lenght of title
	 */
	$maxL = 0;
	foreach ($periods as $period)
	{
	    if (strlen($period) > $maxL)
	    {
		$maxL = strlen($period);
	    }
	    if ($maxL > 10)
	    {
		$sp = 8;
	    }
	    else
	    {
		$sp = 15;
	    }
	}

        /**
         * setting skip step
         */
        if (count($periods) > ($sp - 7) && count($periods) < $sp)
        {
            $c = 1;
        }
        else if (count($periods) >= $sp)
        {
            $c = ceil(count($periods) / $sp);
        }
        else
        {
            $c = 0;
        }

        /**
         * skipping some x labels for good reading
         */
        $i=0;
        $xLabels = array();
        foreach ($periods as $k => $d)
        {
            if ($i == $c && $k != 0)
            {
                $xLabels[$k] = $d;
                $i = 0;
            } 
            elseif($i == $c)
            {
                $xLabels[$k] = '';
                $i = 0;
            }
            else
            {
                $xLabels[$k] = '';
                $i++;
            }
        }                                

        # Set up axis labels
        $params['chxt'] = 'x,y';

        # Set up x Labels
        $params['chxl'] = '0:|';
        $dataDelimiter = '|';
        $is_first = true;
        foreach ( $xLabels as $period )
        {
            if ( !$is_first )
            {
                $params['chxl'] .= $dataDelimiter;
            }
            $params['chxl'] .= $period;
            $is_first = false;            
        }


        # Set up lines
        if ($minValue >= 0 && $maxValue >= 0)
        {
            $minY = 0;
            if ($maxValue > 10)
            {
                $p = pow(10, $this->_getPow($maxValue));
                $maxY = (ceil($maxValue/$p))*$p;
                $yLabels = range($minY, $maxY, $p);
            } 
            else
            {
                $maxY = ceil($maxValue+1);
                $yLabels = range($minY, $maxY, 1);
            }
            $yrange = $maxY;
            $yorigin = 0;
        }
        
        $deltaX = 100 / (count($periods) - 1);
        if ( sizeof($yLabels) > 1 )
        {
            $deltaY = 100 /( sizeof($yLabels) - 1 );
        }
        else
        {
            $deltaY = 100;
        }
        
        $params['chg'] = $deltaX.','.$deltaY.',2,1';

        # Set up y axis
        $params['chxl'] .= '|1:|';
        $dataDelimiter = '|';
        $is_first = true;
        foreach ( $yLabels as $label )
        {
            if ( !$is_first )
            {
                $params['chxl'] .= $dataDelimiter;
            }
            $params['chxl'] .= $label;
            $is_first = false;
        }

        # Set up data
        $params['chd'] = 't:';
        $dataDelimiter = ',';
        $coof = $maxY / 100;
        $is_first = true;
        foreach ( $values as $value )
        {
            if ( !$is_first )
            {
                $params['chd'] .= $dataDelimiter;
            }
            $params['chd'] .= $coof != 0 ? round( $value / $coof ) : '0';
            $is_first = false;
        }

        # Return URL
        $url = self::API_URL;
        $is_first = true;
        foreach ($params as $key=>$param)
        {
            $url .= $is_first ? '?' : '&';
            $url .= $key.'='.$param;
            $is_first = false;
        }
        return $url;        
    }

    public function getMultyLineChartUrl($directUrl = true)
    {
	# try to prevent foreach() errors
	if ( !$this->_values || !$this->_keys )
	{
	    return '';
	}

        $params = array(
            'cht'  => 'lc',
            'chf'  => 'bg,s,'.Mage::helper('advancedreports')->getChartBackgroundColor(),
//            'chm'  => 'B,f4d4b2,0,0,0', # it s background like in standard
            'chs'  => $this->_width.'x'.$this->_height,
        );


        # Set up labels style
        $params['chxs'] = '0,'.Mage::helper('advancedreports')->getChartFontColor().','.Mage::helper('advancedreports')->getChartFontSize().'|1,'.Mage::helper('advancedreports')->getChartFontColor().','.Mage::helper('advancedreports')->getChartFontSize();

        # Getting data
        $values = array();
        $periods = array();
        $maxValue = 0;
        $minValue = 0;

//        echo '<pre>';
//        print_r($this->_values);
//        echo '</pre>';
        foreach ( $this->_values as $row )
        {
            $periods[] = $row['period'];
            $arr = array();
            foreach ( $this->_keys as $key )
            {     
                $arr[$key] = $row[$key];
                if ( isset($row[$key]) && $row[$key] > $maxValue )
                {
                    $maxValue = $row[$key];
                }
                if ( isset($row[$key]) && $row[$key] < $minValue )
                {
                    $minValue = $row[$key];
                }
                
            }
            $values[]  = $arr;
        }        
        
        #setup line colors
        $params['chco'] = '';        
        $arr = array_merge( array( Mage::helper('advancedreports')->getChartColor() ) , $this->_multyLineColors);
        if ( count( $this->_keys ) < count( $this->_multyLineColors ) )
        {
            array_splice($arr, count( $this->_keys ));
        }
        else
        {
            for ( $i = 0; $i < ( count( $this->_keys ) - count( $this->_multyLineColors ) ); $i++ ) 
            {                
                $arr[] = $this->_getRandomColor();
            }
        }

        $is_first = true;
        foreach ( $arr as $color )
        {
            if (!$is_first)
            {
                $params['chco'] .= ',';
            }
            $params['chco'] .= $color;
            $is_first = false;
        }
        

		/*
		 * Get may lenght of title
		 */
		$maxL = 0;
		foreach ($periods as $period)
		{
		    if (strlen($period) > $maxL)
		    {
				$maxL = strlen($period);
		    }
		    if ($maxL > 10)
		    {
				$sp = 8;
		    }
		    else
		    {
				$sp = 15;
		    }
		}

        /**
         * setting skip step
         */
        if (count($periods) > ($sp - 7) && count($periods) < $sp)
        {
            $c = 1;
        }
        else if (count($periods) >= $sp)
        {
            $c = ceil(count($periods) / $sp);
        }
        else
        {
            $c = 0;
        }
        /**
         * skipping some x labels for good reading
         */
        $i=0;
        $xLabels = array();
        foreach ($periods as $k => $d)
        {
            if ($i == $c && $k != 0)
            {
                $xLabels[$k] = $d;
                $i = 0;
            }
            elseif($i == $c)
            {
                $xLabels[$k] = '';
                $i = 0;
            }
            else
            {
                $xLabels[$k] = '';
                $i++;
            }
        }

        # Set up axis labels
        $params['chxt'] = 'x,y';

        # Set up x Labels
        $params['chxl'] = '0:|';
        $dataDelimiter = '|';
        $is_first = true;
        foreach ( $xLabels as $period )
        {
            if ( !$is_first )
            {
                $params['chxl'] .= $dataDelimiter;
            }
            $params['chxl'] .= $period;
            $is_first = false;
        }                

        # Set up lines
        if ($minValue >= 0 && $maxValue >= 0)
        {
            $minY = 0;
            if ($maxValue > 10)
            {
                $p = pow(10, $this->_getPow($maxValue));
                $maxY = (ceil($maxValue/$p))*$p;
                $yLabels = range($minY, $maxY, $p);
            }
            else
            {
                $maxY = ceil($maxValue+1);
                $yLabels = range($minY, $maxY, 1);
            }
            $yrange = $maxY;
            $yorigin = 0;
        }

        $deltaX = 100 / (count($periods) - 1);
        if ( sizeof($yLabels) > 1 )
        {
            $deltaY = 100 /( sizeof($yLabels) - 1 );
        }
        else
        {
            $deltaY = 100;
        }

        $params['chg'] = $deltaX.','.$deltaY.',2,1';

        # Set up y axis
        $params['chxl'] .= '|1:|';
        $dataDelimiter = '|';
        $is_first = true;
        foreach ( $yLabels as $label )
        {
            if ( !$is_first )
            {
                $params['chxl'] .= $dataDelimiter;
            }
            $params['chxl'] .= $label;
            $is_first = false;
        }

//        echo '<pre>';
//        print_r($values);
//        echo '</pre>';

        # Set up data
        $params['chd'] = 't:';
        $dataDelimiter = ',';
        $coof = $maxY / 100;
        
        $is_key_first = true;
        foreach ( $this->_keys as $key )
        {
            if ( !$is_key_first )
            {
                $params['chd'] .= '|';
            }
            $is_first = true;
            foreach ( $values as $value )
            {
                if ( !$is_first )
                {
                    $params['chd'] .= $dataDelimiter;
                }
                $params['chd'] .= $coof != 0 ? round( $value[$key] / $coof ) : '0';
                $is_first = false;
            }                        
            $is_key_first = false;
        }
        
        #set up legends
        $params['chdl'] = '';
        $dataDelimiter = '|';
        $is_first = true;
        foreach ( $this->_keys as $key )
        {
            if ( !$is_first )
            {
                $params['chdl'] .= $dataDelimiter;
            }
	    if (isset($this->_labels))
	    {
		$params['chdl'] .= str_replace('&', ' ', $this->_getLabelByKey($key));
	    }
	    else
	    {
		$params['chdl'] .= str_replace('&', ' ', Mage::helper('advancedreports')->getProductNameBySku($key)); 
	    }	                 
            $is_first = false;
        }        

        # Return URL
        $url = self::API_URL;
        $is_first = true;
        foreach ($params as $key=>$param)
        {
            $url .= $is_first ? '?' : '&';
            $url .= $key.'='.$param;
            $is_first = false;
        }
        return $url;
    }

    protected function _getPow($number)
    {
        $pow = 0;
        while ($number >= 10) {
            $number = $number/10;
            $pow++;
        }
        return $pow;
    }
}
