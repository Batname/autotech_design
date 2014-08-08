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


class JoomlArt_JmSlideshow_Model_System_Config_Source_ListMaskAnimation
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'', 'label'=>Mage::helper('joomlart_jmslideshow')->__('None')),
        	array('value'=>'fade', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Fade')),
            array('value'=>'vrtslide', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Vertical Slide')),
            array('value'=>'hrzslide', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Horizontal Slide')),
            array('value'=>'vrtslidefade', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Vertical Slide & Fade')),
            array('value'=>'hrzslidefade', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Horizontal Slide & Fade'))
        );
    }    
}
