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


class JoomlArt_JmSlideshow_Model_System_Config_Source_ListTypeShowDescwhen
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'always', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Always')),
            array('value'=>'mouseenter', 'label'=>Mage::helper('joomlart_jmslideshow')->__('Mouse Over Image'))
        );
    }    
}
