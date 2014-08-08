<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Block_Adminhtml_Catalog_Product_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	
	public function __construct() {  
		 
		parent::__construct ();
		$this->_objectId = 'id';
		$this->_blockGroup = 'callforprice';
		$this->_controller = 'adminhtml_catalog_product';		
		$this->_headerText = Mage::helper ( 'callforprice' )->__ ( 'Call For Price' );
		$this->_mode = 'edit';		
		$this->_updateButton('save', 'label', Mage::helper('callforprice')->__('Update'));		 		
	}
	
	 
}
