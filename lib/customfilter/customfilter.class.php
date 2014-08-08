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

    class Customfilter 
    {
		var $url = null;
		public function genListCats(){
			$currentcate= Mage::getModel('catalog/layer')->getCurrentCategory();
			$this->url = $currentcate->getUrl();
			
			$child_cates= $currentcate->getChildrenCategories();
			$html='<ol>';
			foreach ($child_cates as $cate){
				$html.='<li class="filter-cat">';
				$html.='<a href="'.$this->url.'?cat='.$cate->getId().'">'.$cate->getName().'</a>';
				if ($cate->getData('children_count')){
					$html.='<div class="filter-showsub"><span>+</span></div>';
					$html.='<div class="filter-subcat" style="height: 0px;overflow: hidden;">';
					$html.= $this->genSubCats($cate);
					$html.='</div>';
				}
				$html.='</li>';
			}
			$html.='</ol>';
			return $html;
		}
		
		public function genSubCats($cate){
			$subcates= $cate->getChildrenCategories();
			$html='';
			foreach ($subcates as $subcate){
				if ($subcate->getIsActive()){
					$html.='<span>';
					$html.='<a href="'.$this->url.'?cat='.$subcate->getId().'">'.$subcate->getName().'</a>';
					$html.='</span>';
					if ($cate->getData('children_count')){
						$html.= $this->genSubCats($subcate);
					}
				}
			}
			unset ($subcates);
			return $html;
		}
	
    }

?>