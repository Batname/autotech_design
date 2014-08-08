<?php
class Magestore_Instantsearch_Block_Instantsearch extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	
	public function setTopSearchTemplate($template)
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Instantsearch')){
			return $this;
		}
		
		$topSearch = $this->getParentBlock();
		if($topSearch != null){
			$topSearch->setTemplate($template);
		}
		return $this;
	}	
         
}