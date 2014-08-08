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

class JoomlArt_JmSlideshow_Block_List extends Mage_Catalog_Block_Product_Abstract
{
	protected $_config = '';
	protected $_listDesc = array();
	protected $_show = 0;
	
	public function __construct($attributes = array())
	{
		$helper = Mage::helper('joomlart_jmslideshow/data');
		$this->_show = $helper->get('show', $attributes);
		if (! $this->_show) {
			return;
		}
		
		parent::__construct();
		
		$this->_config = $helper->get($attributes);
	}
	
	protected function _prepareLayout()
	{
		if ($this->_show) {
			$headBlock = $this->getLayout()->getBlock('head');
			
			if ($this->getConfig('animation') == 'vrtaccordion' || $this->getConfig('animation') == 'hrzaccordion') {
				$headBlock->addCss('joomlart/jmslideshow/css/jquery-ui/jquery-ui.css');
				
				if ($this->getConfig('animation') == 'hrzaccordion') {
					$headBlock->addCss('joomlart/jmslideshow/css/jquery-ui/jquery.hrzAccordion.defaults.css');
				}
			}
		}
		
		return parent::_prepareLayout();
	}
	
	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	function getConfig($key)
	{
		return $this->_config[$key];
	}
	
	/**
	 * overrde the value of the extension's configuration
	 *
	 * @return string
	 */
	function setConfig($key, $value)
	{
		$this->_config[$key] = $value;
		return $this;
	}
	/**
	 * Rendering block content
	 *
	 * @return string
	 */
	function _toHtml()
	{
		$helper = Mage::helper('joomlart_jmslideshow/data');

		$this->_config = $helper->get($this->getData());
		if (! $this->_show || ! $this->getConfig('show')) {
			return;
		}

        $config =  $helper->get();
     	$detect = Mage::helper ( 'joomlart_jmslideshow/mobiledetect' );
		$this->_config["mainWidthtablet"] = $config["mainWidthtablet"];
		$this->_config["mainWidthmobile"] = $config["mainWidthmobile"];
      	if($detect->isTablet()){
			if($this->_config["mainWidthtablet"]) $this->_config["mainWidth"] =  $this->_config["mainWidthtablet"];
	    }elseif($detect->isMobile()){
	        if($this->_config["mainWidthmobile"]) $this->_config["mainWidth"] =  $this->_config["mainWidthmobile"];	 
		}

		$this->__renderSlideShow();
		// check the animation used ?
		if ($this->getConfig('animation') == 'vrtaccordion' || $this->getConfig('animation') == 'hrzaccordion') {
			$this->_config['template'] = 'joomlart/jmslideshow/accordion.phtml';
		} else {
			$this->_config['template'] = 'joomlart/jmslideshow/basic.phtml';
		}
		
		// render html
		$this->assign('config', $this->_config);
		$this->setTemplate($this->_config['template']);
		
		return parent::_toHtml();
	}
	
	/**
	 * render block content for the slideshow
	 */
	private function __renderSlideShow()
	{

		if ($this->getConfig('source') == 'products') {
			// products list
			$products = $this->getListProducts();
			
			$urls = array();
			$items = array();
			
			if ($products) {
				$jmimage = Mage::helper('joomlart_jmslideshow/jmimage')->setConfig($this->getConfig('thumbImgMode'), $this->getConfig('useRatio'));
				
				$i = 0;
				foreach ($products as $k => $_product) {
					$items[$i] = array();
					
					// product link
					$urls[$i] = $_product->getProductUrl();
					
					// minor thumbnail
					$items[$i]['thumb'] = '<img align="left" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $jmimage->resizeThumb('media/catalog/product' . $_product->getImage(), $this->getConfig('thumbImgWidth'), $this->getConfig('thumbImgHeight')) . '" alt="' . $this->htmlEscape($_product->getName()) . '" width="' . $this->getConfig('thumbImgWidth') . '" height="' . $this->getConfig('thumbImgHeight') . '" />';
					
					// main thumbnail
					$items[$i]['mainThumb'] = '<img align="left" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $jmimage->resizeThumb('media/catalog/product' . $_product->getImage(), $this->getConfig('mainWidth'), $this->getConfig('mainHeight')) . '" alt="' . $this->htmlEscape($_product->getName()) . '" width="' . $this->getConfig('mainWidth') . '" height="' . $this->getConfig('mainHeight') . '" />';
					
					// product title and short description
					$items[$i++]['caption'] = '<h3>' . $this->htmlEscape($_product->getName()) . '</h3><p>' . substr($this->htmlEscape($_product->getShortDescription()), 0, 60) . '...' . '</p>';
				}
			}
		} else {
			// images list
			$items = array();
			
			$descriptionArray = $mainsThumbs = $imageArray = array();
			$thumbArray = array();
			$captionsArray = array();
			$urls = array();
			$titles = array();
			$listImgs = $this->getFileInDir();
			if ($this->_config['showDesc']) {
				$descriptionArray = $this->parseDescNew($this->getConfig('description'));
				if (! count($descriptionArray)) {
					$descriptionArray = $this->parseDescOld($this->getConfig('description'));
				}
			}
			
			if (count($listImgs) > 0) {
				foreach ($listImgs as $k => $img) {
					$imageArray[] = $this->_config['folder'] . '/' . $img;
					if ($this->_config['showDesc']) {
						$captionsArray[] = (isset($descriptionArray) && isset($descriptionArray[$img]) && isset($descriptionArray[$img]['caption'])) ? str_replace("'", "\'", $descriptionArray[$img]['caption']) : '';
					}
					$url = (isset($descriptionArray[$img]) && isset($descriptionArray[$img]['url'])) ? $descriptionArray[$img]['url'] : '';
					$title = (isset($descriptionArray[$img]) && isset($descriptionArray[$img]['title'])) ? $descriptionArray[$img]['title'] : '';
					$titles[] = $title;
					$alt = (isset($descriptionArray[$img]) && isset($descriptionArray[$img]['alt'])) ? $descriptionArray[$img]['alt'] : '';
					$alts[] = $alt;
					$urls[] = $url;
				}
				$mainsThumbs = $this->buildThumbnail($imageArray, $this->_config['mainWidth'], $this->_config['mainHeight']);
			}
			
			//Build thumbnail
			if ($this->getConfig('thumbType') == 'thumbs') {
				if (function_exists('imagecreatetruecolor')) {
					$thumbArray = $this->buildThumbnail($imageArray, $this->getConfig('thumbImgWidth'), $this->getConfig('thumbImgHeight'));
				} else {
					$thumbArray = $imageArray;
				}
			}
			
			$items = array();
			if ($listImgs) {
				$items = $listImgs;
				
				for ($i = 0; $i < count($listImgs); $i++) {
					$items[$i] = array();
					
					if (isset($thumbArray[$i])&&$thumbArray[$i])
						$thumbUrl= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $thumbArray[$i];
					else $thumbUrl='';
					// minor thumbnail
					$items[$i]['thumb'] = '<img src="' . $thumbUrl . '" alt="Photo Thumb" width="' . $this->getConfig('thumbImgWidth') . '" height="' . $this->getConfig('thumbImgHeight') . '" />';
					
					if (isset($mainsThumbs[$i])&&$mainsThumbs[$i])
						$mainThumbUrl= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $mainsThumbs[$i];
					else $mainThumbUrl='';
					// main thumbnail
					$items[$i]['mainThumb'] = '<img src="' . $mainThumbUrl . '" alt="'.$alts[$i].'" width="' . $this->getConfig('mainWidth') . '" height="' . $this->getConfig('mainHeight') . '"/>';
					
					// product title and description
					if (isset($captionsArray[$i])&&$captionsArray[$i])
					$items[$i]['caption'] = $captionsArray[$i];
				}
			}
		}

		$this->assign('titles', $titles);
		$this->assign('urls', $urls);
		$this->assign('items', $items);
	}
	
	function getFileInDir()
	{
		if (! $this->_config['folder']) {
			return;
		}
		
		$imagePath = Mage::getBaseDir() . DIRECTORY_SEPARATOR . $this->_config['folder'];
		
		/*Get template color - work with JA Template */
		$color = '';
		if (isset($_GET['ja_color'])) {
			$color = trim($_GET['ja_color']);
		} else {
			global $tmpTools;
			if (isset($tmpTools) && isset($tmpTools->template)) {
				$color = $tmpTools->getParam('ja_color');
			}
		}
		if ($color && is_dir($imagePath . DIRECTORY_SEPARATOR . $color)) {
			$this->_config['folder'] = $this->_config['folder'] . '/' . $color;
			$imagePath = $imagePath . DIRECTORY_SEPARATOR . $color;
		}
		
		if (! is_dir($imagePath)) {
			return array();
		}
		$imgFiles = $this->files($imagePath);
		
		$folderPath = $imagePath . DIRECTORY_SEPARATOR;
		$list = array();
		
		foreach ($imgFiles as $file) {
			$i_f = $imagePath . '/' . $file;
			if (preg_match("/bmp|gif|jpg|png|jpeg/", $file) && is_file($i_f)) {
				$list[] = $file;
			}
		}
		return $list;
	}
	
	function buildThumbnail($imageArray, $twidth, $theight)
	{		
		$thumbImgMode = $this->_config['thumbImgMode'];
		if ($thumbImgMode != 'none') {
			$thumbs = array();
			$jaimage = Mage::helper('joomlart_jmslideshow/jmimage');
			
			$aspect = $this->_config['useRatio'];
			$crop = $thumbImgMode == 'crop' ? true : false;
			foreach ($imageArray as $image) {
				$thumbs[] = $jaimage->resize($image, $twidth, $theight, $crop, $aspect);
			}
		} else {
			return $imageArray;
		}
		return $thumbs;
	}
	
	function files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
	{
		// Initialize variables
		$arr = array();
		// Is the path a folder?
		if (! is_dir($path)) {
			return array();
		}
		// read the source directory
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false) {
			$dir = $path . DIRECTORY_SEPARATOR . $file;
			$isDir = is_dir($dir);
			if (($file != '.') && ($file != '..') && (! in_array($file, $exclude))) {
				if ($isDir) {
					if ($recurse) {
						if (is_integer($recurse)) {
							$recurse--;
						}
						$arr2 = $this->files($dir, $filter, $recurse, $fullpath);
						$arr = array_merge($arr, $arr2);
					}
				} else {
					if (preg_match("/$filter/", $file)) {
						if ($fullpath) {
							$arr[] = $path . '/' . $file;
						} else {
							$arr[] = $file;
						}
					}
				}
			}
		}
		closedir($handle);
		asort($arr);
		return $arr;
	}
	
	function parseDescOld($description)
	{
		$description = str_replace("<br />", "\n", $description);
		$description = explode("\n", $description);
		$descriptionArray = array();
		foreach ($description as $desc) {
			if ($desc) {
				$list = explode(":", $desc, 2);
				$list[1] = (count($list) > 1) ? explode("&", $list[1]) : array();
				$temp = array();
				for ($i = 0; $i < count($list[1]); ++$i) {
					$l = explode("=", $list[1][$i]);
					if (isset($l[1])) {
						$temp[trim($l[0])] = trim($l[1]);
					}
				}
				$descriptionArray[$list[0]] = $temp;
			}
		}
		return $descriptionArray;
	}
	
	function parseDescNew($description)
	{
		$regex = '#\[desc ([^\]]*)\]([^\[]*)\[/desc\]#m';
		
		preg_match_all($regex, $description, $matches, PREG_SET_ORDER);
		
		$descriptionArray = array();
		foreach ($matches as $match) {
			$params = $this->parseParams($match[1]);
			if (is_array($params)) {
				$img = isset($params['img']) ? trim($params['img']) : '';
				if (! $img) {
					continue;
				}
				$url = isset($params['url']) ? trim($params['url']) : '';
				$title = isset($params['title']) ? trim($params['title']) : '';
				$url = isset($params['url']) ? trim($params['url']) : '';
				$alt = isset($params['alt']) ? trim($params['alt']) : '';
				$descriptionArray[$img] = array('url' => $url, 'caption' => str_replace("\n", "<br />", trim($match[2])),'title' => $title,'alt' => $alt);
			}
		}
		return $descriptionArray;
	}
	
	function parseParams($params)
	{
		$params = html_entity_decode($params, ENT_QUOTES);
		$regex = "/\s*([^=\s]+)\s*=\s*('([^']*)'|\"([^\"]*)\"|([^\s]*))/";
		preg_match_all($regex, $params, $matches);
		$paramarray = null;
		if (count($matches)) {
			$paramarray = array();
			for ($i = 0; $i < count($matches[1]); $i++) {
				$key = $matches[1][$i];
				$val = $matches[3][$i] ? $matches[3][$i] : ($matches[4][$i] ? $matches[4][$i] : $matches[5][$i]);
				$paramarray[$key] = $val;
			}
		}
		return $paramarray;
	}
	
	function set($params)
	{
		$params = preg_split("/\n/", $params);
		foreach ($params as $param) {
			$param = trim($param);
			if (! $param) {
				continue;
			}
			$param = explode("=", $param, 2);
			if (count($param) == 2 && strlen(trim($param[1])) > 0) {
				$this->_config[trim($param[0])] = trim($param[1]);
			}
		}
	
	}
	
	function set_($title = '',
		$folder = '',
		$mainWidth = '',
		$mainHeight = '',
		$showDesc = '',
		$descTrigger = '',
		$readMoreText = '',
		$duration = '',
		$animation = '',
		$thumbType = '',
		$thumbImgWidth = '',
		$thumbImgHeight = '',
		$thumbSpaces = '',
		$showItem = '',
		$control = '',
		$autoplay = '',
		$interval = '',
		$thumbOpacity = '',
		$descOpacity = '',
		$description = '',
		$loadJQuery = 1,
		$useRatio,
		$thumbImgMode,
		$useRatios)
	{
		$arrayParams = array(
			'title',
			'folder',
			'mainWidth',
			'mainHeight',
			'showDesc',
			'descTrigger',
			'readMoreText',
			'duration',
			'animation', 
			'thumbType',
			'thumbImgWidth',
			'thumbImgHeight',
			'thumbSpaces',
			'showItem',
			'control',
			'autoplay',
			'interval',
			'thumbOpacity',
			'descOpacity',
			'overlapOpacity',
			'description',
			'loadjquery',
			'useRatio',
			'thumbImgMode'
		);
		$vars = func_get_args();
		foreach ($vars as $k => $var) {
			$this->_config[$arrayParams[$k]] = $var;
		}
	}
	
	function getListProducts()
	{
		$listall = null;
		switch ($this->getConfig('sourceProductsMode')) {
			case 'latest':
				$listall = $this->getListBestBuyProducts('updated_at', 'desc');
				break;
			case 'attribute':
				$listall = $this->getListFeaturedProducts();
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
			default:
				$listall = $this->getListBestBuyProducts('updated_at', 'desc');
				break;
		}
		
		return $listall;
	}
	
	function getListTopRatedProducts($orderfield = 'rating_summary', $order = 'desc', $perPage = NULL, $currentPage = 1)
	{
		$list = null;
		if ($perPage === NULL) {
			$perPage = (int) $this->getConfig('quanlity');
		}
		
		$storeId = Mage::app()->getStore()->getId();
		
		$entityCondition = '_reviewed_order_table.entity_id = e.entity_id';
		
		if ($this->_config['catsid']) {
			// get array of product_id
			$arr_productids = $this->getProductByCategory();
			
			$products = Mage::getResourceModel('catalog/product_collection')->setStoreId($storeId)->addAttributeToSelect('*')->addStoreFilter($storeId)->addIdFilter($arr_productids);
		} else {
			$products = Mage::getResourceModel('catalog/product_collection')->setStoreId($storeId)->addAttributeToSelect('*')->addStoreFilter($storeId);
		}
		
		$products->getSelect()->joinLeft(array('_reviewed_order_table' => $products->getTable('review_entity_summary')), "_reviewed_order_table.store_id=$storeId AND _reviewed_order_table.entity_pk_value=e.entity_id", array());
		
		$products->getSelect()->order("_reviewed_order_table.$orderfield $order");
		$products->getSelect()->group('e.entity_id');
		
		$products->setPageSize($perPage)->setCurPage($currentPage);
		
		$this->setProductCollection($products);
		
		$this->_addProductAttributesAndPrices($products);
		
		if (($_products = $this->getProductCollection()) && $_products->getSize()) {
			$list = $products;
		}
		
		return $list;
	}
	
	function getListMostViewedProducts($perPage = NULL, $currentPage = 1)
	{
		/*
			Show all the product list in the current store			
		*/
		$storeId = Mage::app()->getStore()->getStoreId();
		$this->setStoreId($storeId);
		if ($perPage === NULL) {
			$perPage = (int) $this->getConfig('quanlity');
		}
		$this->_productCollection = Mage::getResourceModel('reports/product_collection');
		
		$this->_productCollection = $this->_productCollection->addViewsCount();
		
		if ($this->_config['catsid']) {
			// get array product_id
			$arr_productids = $this->getProductByCategory();
			
			$this->_productCollection = $this->_productCollection->addAttributeToSelect('*')->setStoreId($storeId)->addStoreFilter($storeId)->addIdFilter($arr_productids);
		} else {
			$this->_productCollection = $this->_productCollection->addAttributeToSelect('*')->setStoreId($storeId)->addStoreFilter($storeId);
		}
		$this->_productCollection->setPageSize($perPage)->setCurPage($currentPage);
		return $this->_productCollection;
	}
	
	function getListBestBuyProducts($fieldorder = 'ordered_qty', $order = 'desc', $product_ids = '', $perPage = NULL, $currentPage = 1)
	{
		$list = null;
		/* 
			Always set de $perPage, by template or by config 
			if $perPage eq 0 (zero) not limit the list
		*/
		if ($perPage === NULL) {
			$perPage = (int) $this->getConfig('quanlity');
		}
		/*
			Show all the product list in the current store
			order by ordered_qty, showing the bestsellers first
		*/
		
		$storeId = Mage::app()->getStore()->getId();
		$resource = Mage::getResourceModel('catalog/product_collection');
		
		$products = $resource->addAttributeToSelect('*')->setStoreId($storeId)->addStoreFilter($storeId)->setOrder($fieldorder, $order);
		if ($this->_config['catsid']) {
			// get array product_id
			$arr_productids = $this->getProductByCategory();
			$products = $products->addIdFilter($arr_productids);
		}
		
		if ($product_ids) {
			$products->addAttributeToFilter('entity_id', $product_ids);
		}
		/*
			Filter list of product showing only the active and 
			visible product
		*/
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		
		$products->setPageSize($perPage)->setCurPage($currentPage);
		
		$this->setProductCollection($products);
		
		$this->_addProductAttributesAndPrices($products);
		
		if (($_products = $this->getProductCollection()) && $_products->getSize()) {
			$list = $_products;
		}
		
		return $list;
	}
	
	function getListFeaturedProducts()
	{
		$list = array();
		// instantiate database connection object
		

		$resource = Mage::getSingleton('core/resource');
		
		$read = $resource->getConnection('catalog_read');
		
		$categoryProductTable = $resource->getTableName('catalog/category_product');
		
		$productEntityIntTable = (string) Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_int';
		
		$eavAttributeTable = $resource->getTableName('eav/attribute');
		
		// Query database for featured product        
		$select = $read->select('cp.product_id')->from(array('cp' => $categoryProductTable))->join(array('pei' => $productEntityIntTable), 'pei.entity_id=cp.product_id', array())->joinNatural(array('ea' => $eavAttributeTable))->where("pei.value='1'")->where("ea.attribute_code='featured'");
		
		//->where($cond_category_id)
		$rows = $read->fetchAll($select);
		
		$product_ids = array();
		if ($rows) {
			foreach ($rows as $row) {
				$product_ids[] = $row['product_id'];
			}
			$list = $this->getListBestBuyProducts('updated_at', 'desc', $product_ids);
		}
		
		return $list;
	}
	
	/**
	 * check the array existed in the other array
	 *
	 */
	function inArray($source, $target)
	{
		for ($j = 0; $j < sizeof($source); $j++) {
			if (in_array($source[$j], $target)) {
				return true;
				//echo 'ok';
			}
		}
	}
	// -- added by congtq 18/09/2009
	

	// ++ added by congtq 27/10/2009
	function getProductByCategory()
	{
		$return = array();
		$pids = array();
		
		$products = Mage::getResourceModel('catalog/product_collection');
		
		foreach ($products->getItems() as $key => $_product) {
			$arr_categoryids[$key] = $_product->getCategoryIds();
			
			if ($this->_config['catsid']) {
				if (stristr($this->_config['catsid'], ',') === FALSE) {
					$arr_catsid[$key] = array(0 => $this->_config['catsid']);
				} else {
					$arr_catsid[$key] = explode(",", $this->_config['catsid']);
				}
				
				$return[$key] = $this->inArray($arr_catsid[$key], $arr_categoryids[$key]);
			}
		}
		
		foreach ($return as $k => $v) {
			if ($v == 1) {
				$pids[] = $k;
			}
		}
		
		return $pids;
	}
	// -- added by congtq 27/10/2009
}
