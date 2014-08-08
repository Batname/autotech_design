<?php

class Magestore_Instantsearch_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getSearchProduct($keyword)
	{
		$result = array();
		
		$limit = Mage::getStoreConfig('instantsearch/general/more_product_num');
		//search by name
		if(Mage::getStoreConfig('instantsearch/general/search_in_name'))
			$products = $this->searchProductByAttribute($keyword,"name");		
		else $products = array();		
		$product_count = count($products);
		
		if($product_count)
		{
			foreach($products as $productId)
			{
				$result[] = $productId;
			}
		}
		
		
		//search by tag
		if($product_count < $limit)
		{
			$product_list2 = $this->searchProductByTag($keyword);
			
			if(count($product_list2))
			{
				
				foreach($product_list2 as $productId)
				{
					$result[] = $productId;
					$product_count++;	
					if($product_count >= $limit)
					{
						break;
					}
				}
			}
		}
		//search by description
		if($product_count < $limit)
		{
			if(Mage::getStoreConfig('instantsearch/general/search_in_description')) {
				$description_type = Mage::getStoreConfig('instantsearch/general/search_description_type');
				switch($description_type) {
				case 1:
					$product_list3 = $this->searchProductByAttribute($keyword,"short_description");
					break;
				case 2:
					$product_list3 = $this->searchProductByAttribute($keyword,"description");
					break;
				case 3:
					$product_list3_1 = $this->searchProductByAttribute($keyword,"short_description");
					$product_list3_2 = $this->searchProductByAttribute($keyword,"description");
					$array_merge = array_merge($product_list3_1,$product_list3_2);
					$product_list3 = array();
					if(count($array_merge)) {
						foreach($array_merge as $item) {
							$product_list3[$item] = $item;
						}
					}
				}
			
			} else $product_list3 = array();
			
			if(count($product_list3))
			{
				
				foreach($product_list3 as $productId)
				{
					$result[] = $productId;
					$product_count++;	
					if($product_count >= $limit)
					{
						break;
					}
				}
			}
		}
		
		$result_final = array();
		if(count($result)) {
			foreach($result as $item)
				$result_final[$item] = $item;
		}
		
		return $result_final;
	}
	
	public function searchProductByAttribute($keyword,$att)
	{
		$result = array();
		
			$limit = Mage::getStoreConfig('instantsearch/general/more_product_num');
			
			$storeId    = Mage::app()->getStore()->getId();
			$products = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('*')		
			->setStoreId($storeId)
			->addStoreFilter($storeId)
			->addFieldToFilter("status",1)	
			->addFieldToFilter($att,array('like'=>'%'. $keyword.'%'))	
			->setPageSize($limit)
			->setCurPage(1)
			->setOrder('name','ASC');
			
			Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($products);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($products);
			$products->load();
			
			if(count($products))
			{
				foreach($products as $product)
				{
					$result[] = $product->getId();
				}
			}
		return $result;
	}
	
	public function searchProductByTag($keyword)
	{
		$result = array();
		
		if(Mage::getStoreConfig('instantsearch/general/search_in_tag'))
		{
			$model = Mage::getModel('tag/tag');
	            $tag_collections = $model->getResourceCollection()
	                ->addPopularity()
	                ->addStatusFilter($model->getApprovedStatus())
					->addFieldToFilter("name",array('like'=>'%'. $keyword.'%'))	
	                ->addStoreFilter(Mage::app()->getStore()->getId())
	                ->setActiveFilter()
	                ->load();
			if(count($tag_collections))
			{
				foreach($tag_collections as $tag)
				{
					$products = $this->getProductListByTagId($tag->getId());
					if(count($products))
					{
						foreach($products as $product)
						{
							$result[] = $product->getId();
						}
					}				
				}
			}
		}	
		return $result;	
	}
	
	public function getProductListByTagId($tagId)
	{
		
		$tagModel = Mage::getModel('tag/tag');
		$collections = $tagModel->getEntityCollection()
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->addTagFilter($tagId)
			->addStoreFilter()
			->addMinimalPrice()
			->addUrlRewrite()
			->setActiveFilter();
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collections);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collections);
		
		return $collections;		
			
	}

   
}