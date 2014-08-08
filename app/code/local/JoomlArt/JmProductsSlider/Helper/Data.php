<?php
class JoomlArt_JmProductsSlider_Helper_Data extends Mage_Core_Helper_Abstract {		
	function get($var, $attributes){
		if(isset($attributes[$var])){
			return $attributes[$var];
		}		
    	return Mage::getStoreConfig("joomlart_jmproductsslider/joomlart_jmproductsslider/$var");
	}	  
}
?>