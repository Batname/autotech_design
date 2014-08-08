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


class JoomlArt_JmSlideshow_Model_System_Config_Source_ListType
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'', 'label'=>Mage::helper('joomlart_jmslideshow')->__('-- Please select --')),
            array('value'=>'latest', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Latest')),
            array('value'=>'best_buy', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Best Buy')),
            array('value'=>'most_viewed', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Most Viewed')),
            array('value'=>'most_reviewed', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Most Reviewed')),
            array('value'=>'top_rated', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Top Rated')),
            array('value'=>'attribute', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Featured Product'))
        );
    }    
}
