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
class AW_Advancedreports_Helper_Additional extends Mage_Core_Helper_Abstract
{	
	const REGISTRY_PATH = 'aw_advancedreports_additional';

	/*
	 * Returns reports factory class
	 */
	public function getReports()
	{
		if (!Mage::registry(self::REGISTRY_PATH)){
			Mage::register(self::REGISTRY_PATH, Mage::getModel('advancedreports/additional_reports'));
		}
		return Mage::registry(self::REGISTRY_PATH);
	}
	
	public function getVersionCheck($item)
	{	
		return version_compare(Mage::helper('advancedreports')->getVersion(), $item->getRequiredVersion(), '>=');
	}
}
