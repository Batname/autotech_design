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


class JoomlArt_JmProductsSlider_Model_System_Config_Backend_JmProductsSlider_checkInputItemheight extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave(){
        $value     = trim($this->getValue());

		if($value!=""){
			if (!is_numeric($value)) { 
				
				throw new Exception(Mage::helper('joomlart_jmproductsslider')->__('Height of Item: Format is incorrect.'));
			}		
		
			if ($value<=0) { 
				throw new Exception(Mage::helper('joomlart_jmproductsslider')->__('Height of Item: must be greater than 0.'));
			}		
		}
        return $value;
         
    }


}
