<?php
/*------------------------------------------------------------------------
# JM Fabian - Version 1.0 - Licence Owner JA155256
# ------------------------------------------------------------------------
# Copyright (C) 2004-2009 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites: http://www.joomlart.com - http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/ 


class JoomlArt_JmSlideshow_Model_System_Config_Source_ListEffect
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'linear', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Linear')),
            array('value'=>'quadOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Medium to Slow')),
            array('value'=>'cubicOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Fast to Slow')),
            array('value'=>'quartOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Very Fast to Slow')),
            array('value'=>'quintOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Uber Fast to Slow')),
            array('value'=>'expoOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Exponential Speed')),
            array('value'=>'elasticOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Elastic')),
            array('value'=>'backIn', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Back In')),
            array('value'=>'backOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Back Out')),
            array('value'=>'backInOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Back In and Out')),
            array('value'=>'bounceOut', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Bouncing')),
        );
    }    
}
