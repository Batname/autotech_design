<?php   
class Wavethemes_Jmquickview_Block_Cartreturn extends Mage_Core_Block_Template{   

    public function _toHtml(){
	    if(Mage::registry('cartreturn')){
		    $this->assign ( 'cartreturn', Mage::registry('cartreturn'));
		    return parent::_toHtml();
		}
	}

}