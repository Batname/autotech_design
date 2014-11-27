<?php

class Sitemaster_CategoryBanner_Model_System_Config_Source_Blockid
{
    protected $_options;

	public function toOptionArray()
	{
        $collection = Mage::getModel('cms/block')->getCollection()
            ->addFieldToFilter('is_active', 1);

        $this->_options = array();

        foreach($collection as $key => $value){
            $block = Mage::getModel('cms/block')->load($key);
            $title = $block->getTitle();
            $this->_options[] = array('value' => $key, 'label' => $title);
        }

        array_push($this->_options, array('value' => '-1','label' => Mage::helper('sitemaster_categorybanner')->__('Default category DisplayMode settings')));

        return $this->_options;
	}

    public function staticblockid()
    {
        $collection = Mage::getModel('cms/block')->getCollection()
            ->addFieldToFilter('is_active', 1);

        $blockCount = $collection->count();
        echo 'Block Count: ' . $blockCount . '<br />'; // just for testing


        foreach($collection as $key => $value){
            $block = Mage::getModel('cms/block')->load($key);
            echo "Key: " . $key . " - " . "Block ID: " . $block->getTitle() . "<br />";
        }
    }
}