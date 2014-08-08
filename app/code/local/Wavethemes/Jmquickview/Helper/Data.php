<?php
class Wavethemes_Jmquickview_Helper_Data extends Mage_Core_Helper_Abstract
{
     function get($var, $attributes){
		if(isset($attributes[$var])){
			return $attributes[$var];
		}		
		return Mage::getStoreConfig("wavethemes_jmquickview/wavethemes_jmquickview/$var");
	 }
}
	 