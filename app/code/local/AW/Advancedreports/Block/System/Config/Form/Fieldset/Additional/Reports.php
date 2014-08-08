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

class AW_Advancedreports_Block_System_Config_Form_Fieldset_Additional_Reports extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	$html = $this->_getHeaderHtml($element);
		if ( count( $items = Mage::getModel('advancedreports/additional_reports')->getReports() ) ){
				
			//TODO Sort by name																
			foreach ($items as $item)
			{				
				$html .= $this->_getFieldHtml(
								$element, 
								$item->getTitle(), 
								$item->getVersion(), 
								!Mage::helper('advancedreports/additional')->getVersionCheck($item));
			}														        					
		} else {
			$html .= Mage::helper('advancedreports')->__('No Additional Reports Installed');
		}
		$html .= $this->_getFooterHtml($element);	
        return $html;
    }

    protected function _getDummyElement()
    {
    	if (empty($this->_dummyElement)) {
    		$this->_dummyElement = new Varien_Object(array('show_in_default'=>1, 'show_in_website'=>1));
    	}
    	return $this->_dummyElement;
    }

    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }

	protected function _getFieldHtml($fieldset, $moduleName, $ver, $is_red = false)
    {
    	$red_open = $is_red ? '<span style="color:red;">' : '';
		$red_close = $is_red ? '</span>' : '';
		$html = '<tr><td class="label"><label>'.$red_open.$moduleName.$red_close.'</label></td><td class="value">'.$red_open.$ver.$red_close.'</td></tr>';
		return $html;	
    }
}

