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


class JoomlArt_JmMasshead_Block_Core extends Mage_Catalog_Block_Product_Abstract {

	var $_config = array ();

	
	public function __construct($attributes = array()) {
		$helper = Mage::helper ( 'joomlart_jmmasshead/data' );
		
		$this->_config ['show'] = $helper->get ( 'show', $attributes );
		if (! $this->_config ['show'])
			return;
		parent::__construct ();
		$this->_config ['title'] = $helper->get ( 'title', $attributes );
		$this->_config ['background'] = $helper->get ( 'background', $attributes );
		$this->_config ['description'] = $helper->get ( 'description', $attributes );		
	}
	protected function _prepareLayout()
	{
		return parent::_prepareLayout();
	}	
	function _toHtml() {
	    
		if (! $this->_config ['show'])
			return;
		
		if($this->gettitle()) $this->_config ['title'] = $this->gettitle();
		if($this->getbackground()) $this->_config ['background'] = $this->getbackground();
		if($this->getdescription()) $this->_config ['description'] = $this->getdescription();
		$this->assign ( 'config', $this->_config );
		if (! isset ( $this->_config ['template'] ) || $this->_config ['template'] == '') {
			$this->_config ['template'] = 'joomlart/jmmasshead/core.phtml';
		}
		$this->setTemplate ($this->_config ['template']);
		return parent::_toHtml ();
	}
	
	
}