<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs  {
	
	public function __construct()
	{
		parent::__construct();
		$this->setId('callforprice_product_tab');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('callforprice')->__('Call For Price Options'));
	}
	
	
	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
				'label'  => Mage::helper('callforprice')->__('Call For Price Settings'),
				'title'  => Mage::helper('callforprice')->__('Call For Price Settings'),				
		));
		return parent::_beforeToHtml();
	}
}