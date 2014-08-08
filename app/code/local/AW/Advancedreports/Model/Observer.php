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
class AW_Advancedreports_Model_Observer
{
	const XML_REPORTS_CHILDREN_PATH = 'adminhtml/menu/report/children';
	const XML_ADDITIONAL_PATH = 'adminhtml/menu/report/children/advancedreports';
	const XML_ACL_ADDITIONAL_PATH = 'adminhtml/acl/resources/admin/children/report/children/advancedreports';
	static $_needSaveCache = false;
	
	public static function execute()
	{
		$reports = Mage::getModel('advancedreports/additional_reports');
		if ($reports->getCount())
		{
			# Load additional menu items
			Varien_Profiler::start('aw::advancedreports::load_additional_menu');
			foreach ($reports->getReports() as $item){				
				if (Mage::helper('advancedreports/additional')->getVersionCheck($item)){
					self::_addMenuItem($item->getName(), $item->getTitle(), "advancedreports_admin/additional_report/index/name/".$item->getName()."/", $item->getSortOrder());
				}				
			}		
			Varien_Profiler::stop('aw::advancedreports::load_additional_menu');

			# Save cache
			if (self::$_needSaveCache && Mage::app()->useCache('config')){
				Varien_Profiler::start('aw::advancedreports::save_cache');
				Mage::getConfig()->saveCache(array('adminhtml'));
				Varien_Profiler::stop('aw::advancedreports::save_cache');				
			}							
		}					
	}	
		
	protected static function _addMenuItem($name, $title, $action, $sort_order = null)
	{
		$node = Mage::getConfig()->getNode(self::XML_ADDITIONAL_PATH.'/children');
		if (!$node){
			Mage::getConfig()->setNode(self::XML_ADDITIONAL_PATH.'/children', NULL);
			$node = Mage::getConfig()->getNode(self::XML_ADDITIONAL_PATH.'/children');
		}
		$max = 0;
		if (is_array($node->asArray()) && count($node->asArray())){
			foreach ($node->asArray() as $element)
			{
				if (isset($element['sort_order'])){
					$max = ($max < $element['sort_order']) ? $element['sort_order'] : $max;
				}
			}				
		}
		try {
			if (!Mage::getConfig()->getNode(self::XML_ADDITIONAL_PATH.'/children/'.$name)){
				# Menu item zone
				Mage::getConfig()->setNode(self::XML_ADDITIONAL_PATH.'/children/'.$name, NULL);			
				$node = Mage::getConfig()->getNode(self::XML_ADDITIONAL_PATH.'/children/'.$name);			
				$node->addAttribute('translate', 'title');
				$node->addAttribute('module', 'advancedreports');	
				Mage::getConfig()->setNode(self::XML_ADDITIONAL_PATH.'/children/'.$name.'/sort_order', $sort_order ? $sort_order : $max + 1);		
				Mage::getConfig()->setNode(self::XML_ADDITIONAL_PATH.'/children/'.$name.'/title', $title);
				Mage::getConfig()->setNode(self::XML_ADDITIONAL_PATH.'/children/'.$name.'/action', $action);		
				# Access zone
				Mage::getConfig()->setNode(self::XML_ACL_ADDITIONAL_PATH.'/children/'.$name, NULL);
				$node = Mage::getConfig()->getNode(self::XML_ACL_ADDITIONAL_PATH.'/children/'.$name);	
				$node->addAttribute('translate', 'title');
				$node->addAttribute('module', 'advancedreports');													
				Mage::getConfig()->setNode(self::XML_ACL_ADDITIONAL_PATH.'/children/'.$name.'/title', $title);
				Mage::getConfig()->setNode(self::XML_ACL_ADDITIONAL_PATH.'/children/'.$name.'/sort_order', $sort_order ? $sort_order : $max + 1);
				self::$_needSaveCache = true;				
			} 						
		} catch (Exception $e){
			
		}			
	}
}



