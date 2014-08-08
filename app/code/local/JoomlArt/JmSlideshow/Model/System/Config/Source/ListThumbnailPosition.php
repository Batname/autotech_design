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


class JoomlArt_JmSlideshow_Model_System_Config_Source_ListThumbnailPosition
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'top', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Top')),
        	array('value'=>'right', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Right')),
        	array('value'=>'bottom', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Bottom')),
        	array('value'=>'left', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Left')),
        	array('value'=>'tl', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Top Left')),
        	array('value'=>'tr', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Top Right')),
        	array('value'=>'bl', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Bottom Left')),
        	array('value'=>'br', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Bottom Right'))
        );
    }    
}
