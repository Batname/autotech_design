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
class AW_Advancedreports_Block_Additional_Grid extends AW_Advancedreports_Block_Advanced_Grid 
{
	/*
	 * Returns report name from registry 
	 */
	protected function _getName()
	{
		return Mage::registry('aw_advancedreports_additional_name');
	}

    public function getChartParams()
    {
        return Mage::helper('advancedreports/additional_'.$this->_getName())->getChartParams( $this->_routeOption );
    }

    public function hasRecords()
    {
        return (count( $this->_customData ) > 1)
               && Mage::helper('advancedreports/additional_'.$this->_getName())->getChartParams( $this->_routeOption )
               && count( Mage::helper('advancedreports/additional_'.$this->_getName())->getChartParams( $this->_routeOption ) );
    }	
	
    public function getNeedReload()
    {
        return Mage::helper('advancedreports/additional_'.$this->_getName())->getNeedReload( $this->_routeOption );
    }		

    public function getNeedTotal()
    {
        return Mage::helper('advancedreports/additional_'.$this->_getName())->getNeedTotal( $this->_routeOption );
    }	
	
}