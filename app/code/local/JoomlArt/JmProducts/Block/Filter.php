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

class JoomlArt_JmProducts_Block_Filter extends Mage_Catalog_Block_Product_Abstract {

	var $_config = array ();
	protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
	
	   /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;
	
	public function __construct($attributes = array()) {
		$helper = Mage::helper ( 'joomlart_jmproducts/data' );
		$mode = $this->getRequest()->getParam('type');
		$viewall = $this->getRequest()->getParam('viewall');

		$this->_config ['show'] = $helper->get ( 'show', $attributes );
		if (! $this->_config ['show'])
			return;
			
		$this->_config ['template'] = $helper->get ( 'template', $attributes );
		if (! $this->_config ['template'])
			return;
		
		parent::__construct ();
		$this->_config ['mode'] = $helper->get ( 'mode', $attributes );
       
		$this->_config ['title'] = $helper->get ( 'title', $attributes );
		$this->_config ['catsid'] = $helper->get ( 'catsid', $attributes );
		$this->_config ['productsid'] = $helper->get ( 'productsid', $attributes );
		$this->_config ['qty'] = $helper->get ( 'quanlity', $attributes );
		$this->_config ['qty'] = $this->_config ['qty'] > 0 ? $this->_config ['qty'] : $listall;
		
		$this->_config ['perrow'] = $helper->get ( 'perrow', $attributes );
		$this->_config ['perrow'] = $this->_config ['perrow'] > 0 ? $this->_config ['perrow'] : 3;
		
		$this->_config['width'] = $helper->get('width', $attributes);
		$this->_config['width'] = $this->_config['width']>0?$this->_config['width']:135;	
		
		$this->_config['height'] = $helper->get('height', $attributes);
		$this->_config['height'] = $this->_config['height']>0?$this->_config['height']:135;	
		
				
		$this->setProductCollection ( $this->getCategory () );
	}
		
	function _toHtml() {
	   
		if (! $this->_config ['show'])
			return;
		$helper = Mage::helper ( 'joomlart_jmproducts/data' );
		$this->_config ['title'] = $this->gettitle();
      
        $listall = $this->getListProducts ();

        $this->assign ( 'listall', $listall );
		$this->assign ( 'config', $this->_config );
           
		if(!$this->getTemplate()){
		  $this->setTemplate('joomlart/jmproducts/filter.phtml');
		}
		if ($listall && $listall->count () > 0) {
			Mage::getModel ( 'review/review' )->appendSummary ( $listall );
		}
		
		return parent::_toHtml ();
	}
	
	function getListProducts() {
	    $listall = null;

	    if(is_null($this->_productCollection)){
		
			switch ($this->_config ['mode']) {
				
				case 'filter':
                    $listall = $this->getListfilterProducts();
					break;
				default:
					 $listall = $this->getListfilterProducts();
			}
		    $this->_productCollection = $listall;
		}	
		
		return $this->_productCollection;
	}
	
	

	function getListfilterProducts($perPage = NULL, $currentPage = 1){
         // get array product_id

           /* 
			Always set de $perPage, by template or by config 
			if $perPage eq 0 (zero) not limit the list
		   */
		   if ($perPage === NULL)
			$perPage = ( int ) $this->_config ['qty'];

		  $params =  $this->getRequest()->getParams();
		  //unset some unrleated params
		  if($params["p"]){
		     unset($params["p"]);
		  }
		   if($params["mode"]){
		     unset($params["mode"]);
		  }
		  unset($params["filter"]);
          if($params["cat"]) {
          	$this->_config["catsid"] = $params["cat"];

          	unset( $params["cat"]);
          } 
          $arr_productids = $this->getProductByCategory(); 
          if($params["price"]){
          	$prices =  explode("-", $params["price"]);
          	$low_price = $prices[0];
          	if($prices[1]){
          		$high_price = $prices[1];
          	}
          	unset($params["price"]);
          }
          unset($params["order"]);
          unset($params["dir"]);
		  unset($params["___store"]);
		  unset($params["limit"]);  
          $storeId = Mage::app ()->getStore ()->getId ();
		  $products = Mage::getResourceModel('catalog/product_collection')
							->setStoreId($storeId)
							->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
							->addMinimalPrice()
							->addFinalPrice()
							->addTaxPercents()
							->addStoreFilter($storeId);

		  if($low_price && $high_price) {

			    $products->addFieldToFilter('price', array('gteq' => $low_price));
			    $products->addFieldToFilter('price', array('lteq' => $high_price));
		   } elseif($high_price) {
			    $products->addAttributeToFilter('price', array('lteq' => $high_price));
		   } elseif($low_price) {
			    $products->addAttributeToFilter('price', array('gteq' => $low_price));
		   } 

		 if(!empty($arr_productids)){
		 	$products->addIdFilter($arr_productids);
		 }					
		 if($params){
		 	foreach($params as $kparam => $vparam){
		 		 $products->addAttributeToFilter($kparam,array('in' => array(0 => (int)$vparam)));
		 	}
		 }				
		$products->setPageSize ( $perPage )->setCurPage ( $currentPage );

		$this->setProductCollection ( $products );
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
           	
		if (($_products = $this->getProductCollection()) && $_products->getSize()){
			$list = $_products ;
		}
		
		return $list;

	}
	
	
	
	
    
    // ++ added by congtq 18/09/2009
    /**
    * check the array existed in the other array
    *
    */
   	function inArray($source, $target) {
		for($j = 0; $j < sizeof ( $source ); $j ++) {
			if (in_array ( $source [$j], $target )) {
				return true;
                //echo 'ok';
			}
		}
	}
	// -- added by congtq 18/09/2009
    
    // ++ added by congtq 27/10/2009
    function getProductByCategory(){
        $return = array(); 
        $pids = array();
        
        $products = Mage::getResourceModel ( 'catalog/product_collection' );
        
        foreach ($products->getItems() as $key => $_product){
            $arr_categoryids[$key] = $_product->getCategoryIds();
            
            if($this->_config['catsid']){    
                if(stristr($this->_config['catsid'], ',') === FALSE) {
                    $arr_catsid[$key] =  array(0 => $this->_config['catsid']);
                }else{
                    $arr_catsid[$key] = explode(",", $this->_config['catsid']);
                }
                
                $return[$key] = $this->inArray($arr_catsid[$key], $arr_categoryids[$key]);
            }
        }
        
        foreach ($return as $k => $v){ 
            if($v==1) $pids[] = $k;
        }    
        
        return $pids;   
    }
   
	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
		   
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
		
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    } 

	 /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }


	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
	    $toolbar = $this->getToolbarBlock();
        $viewall = $this->getData("viewall")?$this->getData("viewall"):false;
		$filter = $this->getData("filter")?$this->getData("filter"):false;
        // called prepare sortable parameters

        //assign some settings from block attributes
        if($this->getData("quanlity")){
        	$this->_config ['qty'] = $this->getData("quanlity");
            
        }
        if($this->getData("perrow")){
        	$this->_config ['perrow'] = $this->getData("perrow");
        }
        if($this->getData("mode")){
        	$this->_config ['mode'] = $this->getData("mode");
        }

        $collection = $this->getListProducts();
		
	    // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        // set collection to toolbar and apply sort
        if(is_object($collection) && (!empty($collection))){
			$toolbar->setCollection($collection);
			$this->setChild('toolbar', $toolbar);
			 Mage::dispatchEvent('catalog_block_product_list_collection', array(
				'collection' => $this->getListProducts()
			));
        }  
       
        return parent::_beforeToHtml();
	
	}
	
	 /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
		
    }
}