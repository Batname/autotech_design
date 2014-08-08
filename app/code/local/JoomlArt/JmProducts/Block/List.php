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

class JoomlArt_JmProducts_Block_List extends Mage_Catalog_Block_Product_Abstract {

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
        $detect = Mage::helper ( 'joomlart_jmproducts/mobiledetect' );
        
		$this->_config ['show'] = $helper->get ( 'show', $attributes );
		if (! $this->_config ['show'])
			return;
			
		$this->_config ['template'] = $helper->get ( 'template', $attributes );
		if (! $this->_config ['template'])
			return;
		
		parent::__construct ();
		$this->_config ['viewall'] = $this->getRequest()->getParam('viewall')?$this->getRequest()->getParam('viewall'):0;
		$this->_config ['mode'] = $helper->get ( 'mode', $attributes );
        if($this->_config ['viewall']){
        	$this->_config ['mode'] = $mode;
        }
        if($this->getRequest()->getParam('filter')){
        	$this->_config ['mode'] = "filter";
        }
		$this->_config ['title'] = $helper->get ( 'headtitle', $attributes );
		$this->_config ['catsid'] = $helper->get ( 'catsid', $attributes );
		$this->_config ['productsid'] = $helper->get ( 'productsid', $attributes );
		$this->_config ['qty'] = $helper->get ( 'quanlity', $attributes );
        $this->_config ['qtytable'] = $helper->get ( 'quanlitytable', $attributes )?$helper->get ( 'quanlitytable', $attributes ):10;
        $this->_config ['qtytableportrait'] = $helper->get ( 'quanlitytableportrait', $attributes )?$helper->get ( 'quanlitytableportrait', $attributes ):10;
        $this->_config ['qtymobile'] = $helper->get ( 'quanlitymobile', $attributes )?$helper->get ( 'quanlitymobile', $attributes ):4;
        $this->_config ['qtymobileportrait'] = $helper->get ( 'quanlitymobileportrait', $attributes )?$helper->get ( 'quanlitymobileportrait', $attributes ):4;
        $this->_config ['qtyperpage'] = $helper->get ( 'quanlityperpage', $attributes )?$helper->get ( 'quanlityperpage', $attributes ):9;
        $this->_config ['qtyperpagetable'] = $helper->get ( 'quanlityperpagetable', $attributes )?$helper->get ( 'quanlityperpagetable', $attributes ):6;
        $this->_config ['qtyperpagemobile'] = $helper->get ( 'qtyperpagemobile', $attributes )?$helper->get ( 'qtyperpagemobile', $attributes ):6;
        $this->_config ['istable'] = 0;
        $this->_config ['ismobile'] = 0;

        $this->_config ['perrow'] = $helper->get ( 'perrow', $attributes );
		$this->_config ['perrow'] = $this->_config ['perrow'] > 0 ? $this->_config ['perrow'] : 3;
		$this->_config ['perrowtablet'] = $helper->get ( 'perrowtablet', $attributes )?$helper->get ( 'perrowtablet', $attributes ):3;
		$this->_config ['perrowmobile'] = $helper->get ( 'perrowmobile', $attributes )?$helper->get ( 'perrowmobile', $attributes ):3;
		$this->_config ['ajaxloadmore'] = $helper->get ( 'ajaxloadmore', $attributes );
		$this->_config ['ajaxloadmoremobile'] = $helper->get ( 'ajaxloadmoremobile', $attributes );
		$this->_config ['ajaxloadmoretable'] = $helper->get ( 'ajaxloadmoretable', $attributes );
		$this->_config ['accordionslider'] = $helper->get ( 'accordionslider', $attributes );

		$this->_config['width'] = $helper->get('width', $attributes);
		$this->_config['width'] = $this->_config['width']>0?$this->_config['width']:135;	
		
		$this->_config['height'] = $helper->get('height', $attributes);
		$this->_config['height'] = $this->_config['height']>0?$this->_config['height']:135;	
		
		$this->_config['max'] = $helper->get('max', $attributes);
		$this->_config['max'] = $this->_config['max']>0?$this->_config['max']:0;
        
		if($detect->isTablet()){
			  $this->_config ['istable'] = 1;
			  $quanlitytable =  $this->_config ['qtytable'];
			  $quanlitytableportrait = $this->_config ['qtytableportrait'];
			  $qtyperpagetable = $this->_config ['qtyperpagetable'];
			  $perrowtable = $this->_config ['perrowtablet'];
			  $ajaxloadmoretable = $this->_config ['ajaxloadmoretable'];
			  $this->_config ['qty'] = $quanlitytable;
			  $this->_config['qtyperpage'] = $qtyperpagetable;
			  $this->_config ['perrow'] = $perrowtable;
			  $this->_config['ajaxloadmore'] = $ajaxloadmoretable;
			  $this->_config ['accordionslider'] = "0";
 
	    }elseif($detect->isMobile()){
         	  $this->_config ['ismobile'] = 1;
	    	  $quanlitymobile =  $this->_config ['qtymobile'] ;
			  $quanlitymobileportrait = $this->_config ['qtymobileportrait'];
			  $qtyperpagemobile = $this->_config ['qtyperpagemobile'];
			  $perrowmobile = $this->_config ['perrowmobile'];
			  $ajaxloadmoremobile = $this->_config ['ajaxloadmoremobile'];
              $this->_config ['qty'] = $quanlitymobile ;
              $this->_config['qtyperpage'] = $qtyperpagemobile;
              $this->_config ['perrow'] = $perrowmobile;
              $this->_config['ajaxloadmore'] = $ajaxloadmoremobile;
              $this->_config ['accordionslider'] = "0";
		}

		$this->_config ['qty'] = $this->_config ['qty'] > 0 ? $this->_config ['qty'] : 30;
		
			
		
		//$this->_config['template'] = $helper->get('template', $attributes);
		/*$this->_config['attributename'] = $helper->get('attributename', $attributes);
		$this->_config['attributevalue'] = $helper->get('attributevalue', $attributes);*/
				
		$this->setProductCollection ( $this->getCategory () );
	}
		
	function _toHtml() {
	   
		if (! $this->_config ['show'])
			return;
		$helper = Mage::helper ( 'joomlart_jmproducts/data' );
		
      
        $listall = $this->getListProducts ();
        $a= $this->_config ['qty'];
        $this->_config ['qty']= "5";
        $listall2 = $this->getListProducts (true);
        $this->_config ['qty'] =$a;

        $this->assign ( 'listall', $listall );
        $this->assign ( 'listall2', $listall2 );
		$this->assign ( 'config', $this->_config );
       
		if(!$this->getTemplate()){
		  $this->setTemplate('joomlart/jmproducts/list.phtml');
		}
		if ($listall && $listall->count () > 0) {
			Mage::getModel ( 'review/review' )->appendSummary ( $listall );
		}
		
		return parent::_toHtml ();
	}
	
	function getListProducts($rerun = false) {
	    $listall = null;
        $helper = Mage::helper ( 'joomlart_jmproducts/data' );
	    if((is_null($this->_productCollection))||($rerun)){
		
			switch ($this->_config ['mode']) {
				case 'latest' :
					$listall = $this->getListBestBuyProducts ('created_at', 'desc' );
					break;
				case 'featured' :
					$listall = $this->getListFeaturedProducts ();
					break;
				case 'best_buy' :
					$listall = $this->getListBestBuyProducts ( 'updated_at', 'desc' );
					break;
				case 'most_viewed' :
					$listall = $this->getListMostViewedProducts ();
					break;
				case 'most_reviewed' :
					$listall = $this->getListTopRatedProducts ( 'reviews_count' );
					break;
				case 'filter':
					$listall = $this->getListfilterProducts();
					break;
				case 'top_rated' :
					$listall = $this->getListTopRatedProducts ();
					break;
				default:
					$listall = $this->getListBestBuyProducts ( 'created_at', 'desc' );
			}
			if($helper->get("random") && is_object($listall)){ 
				$listall->getSelect()->order(new Zend_Db_Expr('RAND()')); 
			} 
		    $this->_productCollection = $listall;

		}	
		
		return $this->_productCollection;
	}
	
	function getListTopRatedProducts($orderfeild = 'rating_summary', $order = 'desc', $perPage = NULL, $currentPage = 1) {
		$list = null;
		
		if ($perPage === NULL)
			$perPage = ( int ) $this->_config ['qty'];
		
        $storeId = Mage::app ()->getStore ()->getId ();
		
		$entityCondition = '_reviewed_order_table.entity_id = e.entity_id';
		
		$products = Mage::getResourceModel('catalog/product_collection')
							->setStoreId($storeId)
							->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
							->addMinimalPrice()
							->addFinalPrice()
							->addTaxPercents()
							->addStoreFilter($storeId);

        if($this->_config['catsid']){
        	$this->addCategoryIdsFilter($products);
        } 
		
		$resource = Mage::getSingleton('core/resource');
        $products->getSelect ()->joinLeft ( array ('_reviewed_order_table' => $resource->getTableName( 'review_entity_summary' )), "_reviewed_order_table.store_id=$storeId AND _reviewed_order_table.entity_pk_value=e.entity_id", array () );
		
		$products->getSelect ()->order ( "_reviewed_order_table.$orderfeild $order" );
		$products->getSelect ()->group ( 'e.entity_id' );
		
        $this->_addProductAttributesAndPrices($products);
		
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
			
		$products->setPageSize ( $perPage )->setCurPage ( $currentPage );        

		if(method_exists($products,"setMaxSize")){
	       $products->setMaxSize($perPage);
		}

		$this->setProductCollection ( $products );

		if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {
		  
		    if(method_exists($_products,"setMaxSize")){
		       $_products->setMaxSize($perPage);
			}
			$list = $_products;
		}
		
		return $list;
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
          
          if($params["price"]){
          	$prices =  explode("-", $params["price"]);
          	$low_price = $prices[0];
          	if($prices[1]){
          		$high_price = $prices[1];
          	}
          	unset($params["price"]);
          }
        
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

		 if($this->_config['catsid']){
        	$this->addCategoryIdsFilter($products);
         } 				
		 if($params){
		 	foreach($params as $kparam => $vparam){
		 		 $products->addAttributeToFilter($kparam, array('eq' => $vparam));
		 	}
		 }		
		 		
		$products->setPageSize ( $perPage )->setCurPage ( $currentPage );        

		if(method_exists($products,"setMaxSize")){
	       $products->setMaxSize($perPage);
		}

		$this->setProductCollection ( $products );
           	
		if (($_products = $this->getProductCollection()) && $_products->getSize()){
			$list = $_products ;
		}
		
		return $list;

	}
	function getListMostViewedProducts($perPage = NULL, $currentPage = 1) {
		/* 
			Always set de $perPage, by template or by config 
			if $perPage eq 0 (zero) not limit the list
		*/
		if ($perPage === NULL)
			$perPage = ( int ) $this->_config ['qty'];
		
        /*
			Show all the product list in the current store			
		*/
		$storeId = Mage::app ()->getStore ()->getStoreId ();
		$this->setStoreId ( $storeId );
		$this->_productCollection = Mage::getResourceModel('reports/product_collection')
										->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
										->addMinimalPrice()
										->addFinalPrice()
										->addTaxPercents()
										->addViewsCount()
										->setStoreId($storeId)
										->addStoreFilter($storeId)
										->setPageSize($perPage);     										
        if($this->_config['catsid']){
        	$this->addCategoryIdsFilter($this->_productCollection);
        } 
		
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);

		$this->_productCollection->setPageSize ( $perPage )->setCurPage ( $currentPage );        

		if(method_exists($this->_productCollection,"setMaxSize")){
	       $this->_productCollection->setMaxSize($perPage);
		}

		if ($this->_productCollection && $this->_productCollection->getSize ()) {
		  
		    if(method_exists($this->_productCollection,"setMaxSize")){
		       $this->_productCollection->setMaxSize($perPage);
			}
		}
        return $this->_productCollection;	
	}
	
	function getListBestBuyProducts($fieldorder = 'ordered_qty', $order = 'desc', $product_ids = '', $perPage = NULL, $currentPage = 1) {
	
		$list = null;
		/* 
			Always set de $perPage, by template or by config 
			if $perPage eq 0 (zero) not limit the list
		*/
		if ($perPage === NULL)
			$perPage = ( int ) $this->_config ['qty'];
			
		
		/*
			Show all the product list in the current store
			order by ordered_qty, showing the bestsellers first
		*/
       
		$storeId = Mage::app ()->getStore ()->getId ();
		$resource = Mage::getResourceModel('catalog/product_collection');
		
		$products = $resource->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
						->addMinimalPrice()
						->addFinalPrice()
						->addTaxPercents()
						->setStoreId($storeId)
						->addStoreFilter($storeId)
						->setOrder($fieldorder, $order);

						
		if($this->_config['productsid']){
            // get array product_id

            $arr_productids = explode(",", $this->_config['productsid']);
            $products = $products->addIdFilter($arr_productids);
		}			
        elseif($this->_config['catsid']){

        	$this->addCategoryIdsFilter($products);
        }      
        
        if ($product_ids) {
			$products->addAttributeToFilter('entity_id', $product_ids);
		}
              
		/*
			Filter list of product showing only the active and 
			visible product
		*/
		Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $products );
		Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
		
		$this->_addProductAttributesAndPrices($products);

		$products->setPageSize ( $perPage )->setCurPage ( $currentPage );        

		if(method_exists($products,"setMaxSize")){
	       $products->setMaxSize($perPage);
		}

		$this->setProductCollection ( $products );

		if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {
			$list = $_products;
		}
		
		return $list;
	}
	
	function getListFeaturedProducts() {
		
		$list = array ();
		// instantiate database connection object
		

		$resource = Mage::getSingleton ( 'core/resource' );
		
		$read = $resource->getConnection ( 'catalog_read' );
		
		$categoryProductTable = $resource->getTableName ( 'catalog/category_product' );
		
		$productEntityIntTable = ( string ) Mage::getConfig ()->getTablePrefix () . 'catalog_product_entity_int';
		
		$eavAttributeTable = $resource->getTableName ( 'eav/attribute' );
		
		// Query database for featured product        
		$select = $read->select ( 'cp.product_id' )->

		from ( array ('cp' => $categoryProductTable ) )->

		join ( array ('pei' => $productEntityIntTable ), 'pei.entity_id=cp.product_id', array () )->

		joinNatural ( array ('ea' => $eavAttributeTable ) )->

		where ( "pei.value='1'" )->

		where ( "ea.attribute_code='featured'" );

		//->where($cond_category_id)
		
		$rows = $read->fetchAll ( $select );
		
		$product_ids = array ();
		if ($rows) {
			foreach ( $rows as $row ) {
				$product_ids [] = $row ['product_id'];
			}
			$list = $this->getListBestBuyProducts ( 'updated_at', 'desc', $product_ids );
		}
		
		return $list;
	}
		
	function set($show=1, $mode='', $title='', $catsid='', $quanlity=9, $perrow=3, $template='', $width ='135', $height='135', $max ='80'){
		if(!$mode || !$show){
            $this->_config['show'] = 0; 
			return ;
		}		
		
		if($mode) $this->_config['mode'] = $mode;
		if($title) $this->_config['title'] = $title;
		if($catsid!='') 	$this->_config['catsid'] = $catsid;
		if($quanlity)		$this->_config['qty'] = $quanlity;
		if($perrow)		$this->_config['perrow'] = $perrow;
		
		// -- added by Duchh 15/12/2009
		if($width)		$this->_config['width'] = $width;
		if($height)		$this->_config['height'] = $height;
		if($max)		$this->_config['max'] = $max;
		//if($attributename)		$this->_config['attributename'] = $attributename;
		//if($attributevalue)		$this->_config['attributevalue'] = $attributevalue;		
        //if($template!='')     $this->_config['template'] = $template;
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

	function addCategoryIdsFilter($products_collection){
        $categories_to_filter = explode(",", $this->_config['catsid']);
		$ctf = array();
		 
		foreach ($categories_to_filter as $k => $cat) {
		     $ctf[]['finset'] = $cat;
		}
		 
		$products_collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
		            ->addAttributeToFilter('category_id',array($ctf))->groupByAttribute('entity_id');
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
    	if (! $this->_config ['show'])
			return;
	    $toolbar = $this->getToolbarBlock();
        $viewall = $this->getData("viewall")?$this->getData("viewall"):false;
		$filter = $this->getData("filter")?$this->getData("filter"):false;
        // called prepare sortable parameters

        //assign some settings from block attributes
        if($this->getData("quanlity")){
        	$this->_config ['qty'] = $this->getData("quanlity");
            
        }
        if($this->getData("catsid")){
        	$this->_config ['catsid'] = $this->getData("catsid");
        }
        if($this->getData("perrow")){
        	$this->_config ['perrow'] = $this->getData("perrow");
        }
        if($this->getData("mode")){
        	$this->_config ['mode'] = $this->getData("mode");
        }
        if($this->getData("title")){
        	$this->_config ['title'] = $this->getData("title");
        }
        if($this->getData("qtyperpage")){
        	$this->_config ['qtyperpage'] = $this->getData("qtyperpage");
        }
        $detect = Mage::helper ( 'joomlart_jmproducts/mobiledetect');
        if($detect->isTablet()){
			  //assign some settings from block attributes
		        if($this->getData("quanlitytable")){
		        	$this->_config ['qty'] = $this->getData("quanlitytable");
		            
		        }
		        if($this->getData("perrowtablet")){
		        	$this->_config ['perrow'] = $this->getData("perrowtablet");
		        }
		        if($this->getData("qtyperpagetable")){
		        	$this->_config ['qtyperpagetable'] = $this->getData("qtyperpagetable");
		        }
 
	    }elseif($detect->isMobile()){
         	   //assign some settings from block attributes
		        if($this->getData("quanlitymobile")){
		        	$this->_config ['qty'] = $this->getData("quanlitymobile");
		            
		        }
		        if($this->getData("perrowmobile")){
		        	$this->_config ['perrow'] = $this->getData("perrowmobile");
		        }
		     
		        if($this->getData("qtyperpage")){
		        	$this->_config ['qtyperpagemobile'] = $this->getData("qtyperpagemobile");
		        }
 
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
        
        if($this->_config ['qtyperpage']){
    		$toolbar->addPagerLimit("grid",$this->_config ['qtyperpage']);
       		$toolbar->addPagerLimit("list",$this->_config ['qtyperpage']);          
        }
        // set collection to toolbar and apply sort
        // set collection to toolbar and apply sort
        if(is_object($collection) && (method_exists($collection,"setMaxSize") || $viewall || $filter)){
			$toolbar->setCollection($collection);
			$this->setChild('toolbar', $toolbar);
			 Mage::dispatchEvent('catalog_block_product_list_collection', array(
				'collection' => $collection
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

    public function getFirstCategoryName($_product)
    {
    	$root_cate_id = Mage::app()->getStore()->getRootCategoryId();
    	$ids = $_product->getCategoryIds();
    	if ((count($ids)>1)&&($ids[0]==$root_cate_id)){
    		$category_id = $ids[1];
    	}else {
    		$category_id = $ids[0];
    	}
    	$cate = Mage::getModel('catalog/category')->load($category_id) ;
    	return $cate->getName();
    }
}