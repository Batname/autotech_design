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

class JoomlArt_JmProductsSlider_Model_System_Config_Source_ListDirection{
    public function toOptionArray()
    {
        return array(        	
            array('value'=>'left', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Left')),
            array('value'=>'right', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Right')),
        );
    }    
}