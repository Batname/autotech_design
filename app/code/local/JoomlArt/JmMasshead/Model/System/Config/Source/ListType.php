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


class JoomlArt_JmProducts_Model_System_Config_Source_ListType
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'', 'label'=>Mage::helper('joomlart_jmproducts')->__('-- Please select --')),
            array('value'=>'latest', 'label'=>Mage::helper('joomlart_jmproducts')->__('Latest')),
            array('value'=>'best_buy', 'label'=>Mage::helper('joomlart_jmproducts')->__('Best Buy')),
            array('value'=>'most_viewed', 'label'=>Mage::helper('joomlart_jmproducts')->__('Most Viewed')),
            array('value'=>'most_reviewed', 'label'=>Mage::helper('joomlart_jmproducts')->__('Most Reviewed')),
            array('value'=>'top_rated', 'label'=>Mage::helper('joomlart_jmproducts')->__('Top Rated')),
            array('value'=>'attribute', 'label'=>Mage::helper('joomlart_jmproducts')->__('Featured Product'))
        );
    }    
}
