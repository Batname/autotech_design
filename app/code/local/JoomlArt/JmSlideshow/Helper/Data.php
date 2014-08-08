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

class JoomlArt_JmSlideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
	function get($attributes=array())
	{
		$data = array();
		$arrayParams = array(
			'show', 
			'title', 
			'loadjquery' ,
			'source',
			'sourceProductsMode',
			'catsid',
			'quanlity',
			'folder',
			'readMoreText', 
			
			'animation', 					//[slide, fade, slice], slide and fade for old compactible
			
			'mainWidth',					//width of main item
			'mainWidthtablet',
			'mainWidthtabletportrait',
			'mainWidthmobile',
			'mainWidthmobileportrait',
			'mainHeight',					//height of main item
			
			'duration',						//duration - time for animation
			
			'autoPlay',						//auto play
			'repeat',						//animation repeat or not
			'interval',						//interval - time for between animation	
			
			'rtl',							//rtl
			
			'thumbType', 					//false - no thumb, other [number, thumb], thumb will animate
			'thumbImgMode',
			'useRatio',
			'thumbImgWidth',
			'thumbImgHeight',
			
			'thumbItems',					//number of thumb item will be show
			'thumbWidth',					//width of thumbnail item
			'thumbHeight',					//width of thumbnail item
			'thumbSpaces',					//space between thumbnails
			'thumbDirection',				//thumb orientation
			'thumbPosition',				//[0%, 50%, 100%]
			'thumbTrigger',					//thumb trigger event, [click, mouseenter]
			
			'controlBox',					//show navigation controller [next, prev, play, playback] - JM does not have a config
			'controlPosition',				//show navigation controller [next, prev, play, playback] - JM does not have a config
			
			'navButtons',					//main next/prev navigation buttons mode, [false, auto, fillspace]
			
			'showDesc',						//show description or not
			'descTrigger',					//[always, mouseover, load]
			'maskWidth',					//mask - a div over the the main item - used to hold descriptions
			'maskHeight',					//mask height
			'maskAnim',						//mask transition style [fade, slide, slide-fade], slide - will use the maskAlign to slide
			'maskOpacity',					//mask opacity
			'maskPosition',					//[0%, 50%, 100%]
			'description',
			
			'showProgress',					//show the progress bar
			
			'urls', 						// [] array of url of main items
			'targets' 						// [] same as urls, an array of target value such as, '_blank', 'parent', '' - default
		);
		
		foreach ($arrayParams as $var) {
			if (isset($attributes[$var])) {
				$data[$var] =  $attributes[$var];
			}
			else {
				$data[$var] =  Mage::getStoreConfig("joomlart_jmslideshow/joomlart_jmslideshow/$var");
			}
		}
		
    	return $data;
	}
}
?>