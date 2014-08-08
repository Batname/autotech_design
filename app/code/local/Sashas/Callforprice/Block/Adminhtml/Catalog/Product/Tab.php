<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Adminhtml_Catalog_Product_Tab extends Mage_Adminhtml_Block_Template  implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	
	public function _construct() {
		parent::_construct();
		$this->setTemplate('callforprice/catalog/product/tab.phtml');
	}
	
	public function getTabLabel(){
		return $this->__('Call for Price');
	}
	
	public function getTabTitle(){
		return $this->__('Click here to edit Call for price settings');
	}
	
	public function canShowTab(){
		return true;
	}
	
	public function isHidden(){
		return false;
	}
	
	public function getTabClass(){
		return 'ajax call-for-price-tab notloaded';
	}
	
	public function getSkipGenerateContent(){
		return false;
	}
	
	public function getTabUrl(){
		return  $this->getUrl('callforprice/admintab/getoptions', array('_current'=>true));
	}
	 
	
}