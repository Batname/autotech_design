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

class JoomlArt_JmProductsSlider_Block_List extends Mage_Catalog_Block_Product_Abstract 
{
	var $_config = array();
	
	protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
	protected $_productCollection;
	
	public function __construct($attributes = array()){
		$helper =  Mage::helper('joomlart_jmproductsslider/data');
		$this->_config['show'] = $helper->get('show', $attributes);
		if(!$this->_config['show']) return;
		$this->_config ['template'] = $helper->get ( 'template', $attributes );
		if (! $this->_config ['template'])
			return;
		parent::__construct();
		
		
			
		$this->_config['mode'] = $helper->get('mode', $attributes);
		$this->_config['title'] = $helper->get('title', $attributes);
		
        $this->_config['height'] = $helper->get('height', $attributes);
		if(!$this->_config['height']) $this->_config['height']=100;
        
        $this->_config['width'] = $helper->get('width', $attributes);
        if(!$this->_config['width']) $this->_config['width']=100;	
		
        $this->_config['itemheight'] = $helper->get('itemheight', $attributes);
		if(!$this->_config['itemheight']) $this->_config['itemheight']= $this->_config['height'] + 50;
        
        $this->_config['itemwidth'] = $helper->get('itemwidth', $attributes);
        if(!$this->_config['itemwidth']) $this->_config['itemwidth']=$this->_config['width'] + 45;
		
		$this->_config['catsid'] = $helper->get('catsid', $attributes);
		
		$this->_config['qty'] = $helper->get('quanlity', $attributes);
		$this->_config['qty'] = $this->_config['qty']>0?$this->_config['qty']:8;	
		
		$this->_config['number_items'] = $helper->get('number_items', $attributes);
		$this->_config['number_items'] = $this->_config['number_items']>0?$this->_config['number_items']:4;	
			
		$this->_config['show_price'] = $helper->get('show_price', $attributes);
		$this->_config['show_readmore'] = $helper->get('show_readmore', $attributes);
		$this->_config['show_desc'] = $helper->get('show_desc', $attributes);
		
		$this->_config['max'] = $helper->get('max', $attributes);
		$this->_config['max'] = $this->_config['max']>0?$this->_config['max']:0;
		
		$this->_config['show_cart'] = $helper->get('show_cart', $attributes);
		//$this->_config['direction'] = $helper->get('direction', $attributes);
		
		$this->_config['use_scrollerbar'] = $helper->get('use_scrollerbar', $attributes);
		$this->_config['delaytime'] = $helper->get('delaytime', $attributes);
		$this->_config['autorun'] = $helper->get('autorun', $attributes);
		if(!$this->_config['delaytime']) $this->_config['autorun'] =0;
		$this->_config['animationtime'] = $helper->get('animationtime', $attributes);
		$this->_config['attributename'] = $helper->get('attributename', $attributes);
		$this->_config['attributevalue'] = $helper->get('attributevalue', $attributes);
		$this->_config['hover'] = $helper->get('hover', $attributes);
		$this->_config['desc'] = $helper->get('desc', $attributes);
	}

	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
		foreach ($this->_config as $key => $value){
    		if ($this->getData($key)){
    			$this->_config [$key] = $this->getData($key);
    		}
    	}
        return parent::_beforeToHtml();
	}

	function _toHtml() {
		if(!$this->_config['show']) return;
         
		$listall = $this->getListProducts();
		
		$this->assign('listall', $listall);		
		$this->assign('configs', $this->_config);
		
		if(!$this->getTemplate()){
		  $this->setTemplate('joomlart/jmproductsslider/list.phtml');
		}

        return parent::_toHtml();	
	}			
		
	function getListProducts(){
		$listall = null;
		if(is_null($this->_productCollection)){
	      	switch ($this->_config['mode']){
				case 'latest':				
					$listall = $this->getListBestBuyProducts( 'updated_at', 'desc');				
					break;
				case 'feature':
					break;
				case 'best_buy':
					$listall = $this->getListBestBuyProducts();
					break;
				case 'most_viewed':
					$listall = $this->getListMostViewedProducts();
					break;
				case 'most_reviewed':
					$listall = $this->getListTopRatedProducts('reviews_count');
					break;
				case 'top_rated':
					$listall = $this->getListTopRatedProducts();
					break;	
				case 'attribute':			
					$listall = $this->getListFeaturedProduct();
					break;	
				case 'category':
					$listall = $this->getListProductbyCatsID();
					break;	
				default:
					$listall = $this->getListBestBuyProducts('updated_at', 'desc');
				
			}
			$this->_productCollection = $listall;
        }   
		return $this->_productCollection;
	}	
	
	function getListTopRatedProducts($orderfeild='rating_summary', $order='desc', $perPage=NULL, $currentPage=1){
		$list = null;
		if($perPage === NULL) $perPage	= (int) $this->_config['qty'];
        
		$storeId = Mage::app()->getStore()->getId();

        $entityCondition = '_reviewed_order_table.entity_id = e.entity_id';
		
        $products = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($storeId)
                ->addAttributeToSelect('*')
                ->addStoreFilter($storeId);              

        if($this->_config['catsid']){
        	$this->addCategoryIdsFilter($products);
        } 
					
		$products->getSelect()->joinLeft(
			                array('_reviewed_order_table'=>$products->getTable('review_entity_summary')),
			                		"_reviewed_order_table.store_id=$storeId AND _reviewed_order_table.entity_pk_value=e.entity_id",
			                array()
			            );
			
		$products->getSelect()->order("_reviewed_order_table.$orderfeild $order");	
        $products->getSelect()->group('e.entity_id');		
        		
		$products->setPageSize($perPage)->setCurPage($currentPage);
		
		$this->setProductCollection($products);
		
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
			
		if (($_products = $this->getProductCollection()) && $_products->getSize()){
			$list = $products;
		}	
		
		return $list;		
	}		
	
	function getListMostViewedProducts(){			
		$list = array();
								
		$perPage	= (int) $this->_config['qty'];
        
		$storeId = Mage::app()->getStore()->getId();

		$products_collection = Mage::getResourceModel('reports/product_collection')
					->setStoreId($storeId)
					->addAttributeToSelect('*')
					->addStoreFilter($storeId)
					->addViewsCount()
                    ->addAttributeToFilter('visibility', 4)
                    ->addAttributeToFilter('status', array('eq' => 1));
		
        if($this->_config['catsid']){
        	$this->addCategoryIdsFilter($products_collection);
        }              
	   
		if ($products_collection && $products_collection->getSize()){
		    $products_collection->getSelect()->limit($perPage);
		}	
		
		$this->setProductCollection($products_collection);

		if (($_products = $this->getProductCollection()) && $_products->getSize()){
			$list = $products_collection;
		}	
		
		return $list;
	}
	
	function getListBestBuyProducts($fieldorder='ordered_qty', $order='desc'){		
		$list = array();

		$perPage	= (int) $this->_config['qty'];
        
		$storeId = Mage::app()->getStore()->getId();

		$products_collection = Mage::getResourceModel('catalog/product_collection')
					->setStoreId($storeId)
					->addAttributeToSelect('*')
					->addStoreFilter($storeId)
                    ->addAttributeToFilter('visibility', 4)
                    ->addAttributeToFilter('status', array('eq' => 1))
					->addAttributeToSort($fieldorder, $order);
		
        if($this->_config['catsid']){
        	$this->addCategoryIdsFilter($products_collection);
        }              
	   
		if ($products_collection && $products_collection->getSize()){
		    $products_collection->getSelect()->limit($perPage);
		}	
		
		$this->setProductCollection($products_collection);

		if (($_products = $this->getProductCollection()) && $_products->getSize()){
			$list = $products_collection;
		}	
		
		return $list;
	}

	function getListFeaturedProduct(){
		$list = array();

		$perPage	= (int) $this->_config['qty'];
        
		$storeId = Mage::app()->getStore()->getId();

		$products_collection = Mage::getResourceModel('catalog/product_collection')
					->setStoreId($storeId)
					->addAttributeToSelect('*')
					->addStoreFilter($storeId)
                    ->addAttributeToFilter('visibility', 4)
                    ->addAttributeToFilter('status', array('eq' => 1))
                    ->addAttributeToSelect('featured');
        $products_collection->addFieldToFilter(array(
											array('attribute'=>'featured','eq'=>'1')		
										));
		
        if($this->_config['catsid']){
        	$this->addCategoryIdsFilter($products_collection);
        }              
	   
		if ($products_collection && $products_collection->getSize()){
		    $products_collection->getSelect()->limit($perPage);
		}	
		
		$this->setProductCollection($products_collection);

		if (($_products = $this->getProductCollection()) && $_products->getSize()){
			$list = $products_collection;
		}	
		
		return $list;
	}
	
	function getListProductbyCatsID($perPage=NULL, $currentPage=1){
		if (!$this->_config['catsid']) return ;
		
		$list = array();
		
		$layer = Mage::getSingleton('catalog/layer');
		
		$categories_id = explode(',', $this->_config['catsid']);
		
		$k = 0;
		foreach ($categories_id as $catid){
			$catid = (int)trim($catid);
			if($catid){
				$category = Mage::getModel('catalog/category')->load($catid);
				
		        if ($category->getId()) {
		            $origCategory = $layer->getCurrentCategory();
		            $layer->setCurrentCategory($category);
		            
		            $product_collection = $layer->getProductCollection();
		            Mage::dispatchEvent('catalog_block_product_list_collection', array(
			            'collection'=>$product_collection,
			        ));
			
			        $product_collection->load();
			        Mage::getModel('review/review')->appendSummary($product_collection);
					Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($product_collection);
					Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($product_collection);
        
		            $list[$k]['items'] 	= $product_collection;
		            $list[$k]['category']  = $category;
		            $k++;		            
		        }
			}
		}
		
		return $list;
	}
			
	function set(
				$show=1, 
				$mode='', 
				$title='', 
				$height='', 
				$width='', 
				$itemheight='', 
				$itemwidth='', 
				$catsid='', 
				$quanlity='', 
				$number_items='', 
				$show_price="", 
				$show_cart='', 
				$use_scrollerbar='', 
				$autorun='', 
				$delaytime='', 
				$animationtime='', 
				$attributename='', 
				$attributevalue='', 
				$template='',
				$max='80')
	{
		if(!$show){
            $this->_config['show'] = 0;
			return ;
		}
		
		if($mode) $this->_config['mode'] = $mode;
		if($title) $this->_config['title'] = $title;
		if($height) $this->_config['height'] = $height;
		if($width) $this->_config['width'] = $width;
		if($height) $this->_config['itemheight'] = $itemheight;
		if($width) $this->_config['itemwidth'] = $itemwidth;
		
        if($catsid)     $this->_config['catsid'] = $catsid;
       // if($template!='') 	$this->_config['template'] = $template;
		if($quanlity)		$this->_config['qty'] = $quanlity;
		if($number_items)		$this->_config['number_items'] = $number_items;
		if($show_price)		$this->_config['show_price'] = $show_price;
		if($show_cart)		$this->_config['show_cart'] = $show_cart;
		if($use_scrollerbar)		$this->_config['use_scrollerbar'] = $use_scrollerbar;
		if($autorun)		$this->_config['autorun'] = $autorun;
		if($delaytime)		$this->_config['delaytime'] = $delaytime;
		if($animationtime)		$this->_config['animationtime'] = $animationtime;
		if($attributename)		$this->_config['attributename'] = $attributename;
		if($attributevalue)		$this->_config['attributevalue'] = $attributevalue;			
		if($template)		$this->_config['template'] = $template;		
		if($max)			$this->_config['max'] = $max;					
	}	
	
	function jmResize($image,$max_width,$max_height, $crop=1){
		$path = Mage::getBaseDir(); 
        if(file_exists($path.'/media/catalog/product/'.$image)){
		    $imgInfo = getimagesize($path.'/media/catalog/product/'.$image);
        }else{
            return '';
        }    
		
		$full_path = $path.'/media/catalog/product/'.$image;
		$width = $imgInfo[0];
		$height = $imgInfo[1];
		if(!$max_width && !$max_height) {
			$max_width = $width;
			$max_height = $height;
		}else{
			if(!$max_width) $max_width = 1000;
			if(!$max_height) $max_height = 1000;
		}
		
		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;
		$dst = new stdClass();
		$src = new stdClass();
		$src->y = $src->x = 0;
		$dst->y = $dst->x = 0;
		if ($crop) {
			$dst->w = $max_width;
			$dst->h = $max_height;
			if (($width <= $max_width) && ($height <= $max_height) ) {
				$src->w = $max_width;
				$src->h = $max_height;
			}else{
				if ($x_ratio < $y_ratio) {
					$src->w = ceil($max_width/$y_ratio);
					$src->h = $height;
				} else {
					$src->w = $width;
					$src->h = ceil($x_ratio*$height);
				}
			}
			$src->x = floor(($width-$src->w)/2);
			$src->y = floor(($height-$src->h)/2);
		} else {
			$src->w = $width;
			$src->h = $height;
			if (($width <= $max_width) && ($height <= $max_height) ) {
				$dst->w = $width;
				$dst->h = $height;
			} else if (($x_ratio * $height) < $max_height) {
				$dst->h = ceil($x_ratio * $height);
				$dst->w = $max_width;
			} else {
				$dst->w = ceil($y_ratio * $width);
				$dst->h = $max_height;
			}
		}

		$ext = strtolower(substr(strrchr($image, '.'), 1)); // get the file extension
		$rzname = strtolower(substr($image, 0, strpos($image,'.')))."_{$dst->w}_{$dst->h}.{$ext}"; // get the file extension
		//
		$resized = $path.'/media/resized/'.$rzname; 
		if(file_exists($resized)){
			$smallImg = getimagesize($resized);
			if (($smallImg[0] <= $dst->w && $smallImg[1] == $dst->h) ||
				($smallImg[1] <= $dst->h && $smallImg[0] == $dst->w)) {
					return "media/resized/".$rzname;
			}
		}
		if(!file_exists($path.'/media/resized/') && !mkdir($path.'/media/resized/',0755)) return '';
		
		$folders = explode('/',strtolower($image));
		$tmppath = $path.'/media/resized/';
		for($i=0;$i < count($folders)-1; $i++){
			if(!file_exists($tmppath.$folders[$i]) && !mkdir($tmppath.$folders[$i],0755)) return '';
			$tmppath = $tmppath.$folders[$i].'/';
		}	

				
		switch ($imgInfo[2]) {
			case 1: $im = imagecreatefromgif($full_path); break;
			case 2: $im = imagecreatefromjpeg($full_path);  break;
			case 3: $im = imagecreatefrompng($full_path); break;
			default: return '';  break;
		}
				
	 	$newImg = imagecreatetruecolor($dst->w, $dst->h);
	 
		/* Check if this image is PNG or GIF, then set if Transparent*/  
		if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
			imagealphablending($newImg, false);
			imagesavealpha($newImg,true);
			$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
			imagefilledrectangle($newImg, 0, 0, $dst->w, $dst->h, $transparent);
		}
		imagecopyresampled($newImg, $im, $dst->x, $dst->y, $src->x, $src->y, $dst->w, $dst->h, $src->w, $src->h);

		//Generate the file, and rename it to $newfilename
		switch ($imgInfo[2]) {
			case 1: imagegif($newImg,$resized); break;
			case 2: imagejpeg($newImg,$resized, 90);  break;
			case 3: imagepng($newImg,$resized); break;
			default: return '';  break;
		}
	 
	 	return "media/resized/".$rzname;

	}
    
    
    // ++ added by congtq 21/09/2009
    /**
    * check the array existed in the other array
    *
    */
    function inArray($source, $target){
        for($j=0; $j<sizeof($source); $j++){
            if(in_array($source[$j], $target)){
                 return true;
            }
        }     
    }

    function addCategoryIdsFilter($products_collection){
        $categories_to_filter = explode(",", $this->_config['catsid']);
		$ctf = array();
		 
		foreach ($categories_to_filter as $k => $cat) {
		     $ctf[]['finset'] = $cat;
		}
		 
		$products_collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
		            ->addAttributeToFilter('category_id',array($ctf))->groupByAttribute('entity_id');
    }
}