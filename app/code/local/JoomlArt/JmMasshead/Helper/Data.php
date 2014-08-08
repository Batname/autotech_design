<?php
/*------------------------------------------------------------------------
# $JA#PRODUCT_NAME$ - Version $JA#VERSION$ - Licence Owner $JA#OWNER$
# ------------------------------------------------------------------------
# Copyright (C) 2004-2009 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites: http://www.joomlart.com - http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/ 

class JoomlArt_JmMasshead_Helper_Data extends Mage_Core_Helper_Abstract {		
	function get($var, $attributes){
		if(isset($attributes[$var])){
			return $attributes[$var];
		}		
    	return Mage::getStoreConfig("joomlart_jmmasshead/joomlart_jmmasshead/$var");
	}	  
}
?>