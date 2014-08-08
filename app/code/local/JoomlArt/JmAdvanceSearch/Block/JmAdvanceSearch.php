<?php
require_once(Mage::getModuleDir('','Mage_CatalogSearch').DS.'Block'.DS.'Advanced'.DS.'Form.php');

class JoomlArt_JmAdvanceSearch_Block_JmAdvanceSearch extends Mage_CatalogSearch_Block_Advanced_Form
{
	public function getStoreCategories()
    {
        $helper = Mage::helper('catalog/category');
        return $helper->getStoreCategories();
    }
}