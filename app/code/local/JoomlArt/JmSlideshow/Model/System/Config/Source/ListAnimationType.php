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


class JoomlArt_JmSlideshow_Model_System_Config_Source_ListAnimationType
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fade', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Fade')),
			array('value'=>'hrzslide', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Horizontal Slide')),
			array('value'=>'vrtslide', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Vertical Slide')),
			array('value'=>'fullslide', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Full Slide')),
			array('value'=>'slice', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Random Slices')),
			array('value'=>'hrzaccordion', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Horizontal Accordion')),
			array('value'=>'vrtaccordion', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Vertical Accordion'))
        );
    }    
}
