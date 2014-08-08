<?php
class JoomlArt_JmAdvanceSearch_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function autoAction()
    {
    	header('Cache-Control: no-cache, must-revalidate');
    	header('Content-type: application/json');
    	
    	$collections = Mage::getResourceModel('catalog/product_collection')
    		->addAttributeToSelect(array('name'))
    		->addAttributeToFilter('name', array('like' => '%'.$this->getRequest()->getParam('query').'%'));
    	
    	if ($this->getRequest()->getParam('cate')!=='all'){
    		$collections->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
    			->addAttributeToFilter('category_id', array(array('finset' => $this->getRequest()->getParam('cate'))));
    	}
    	$max = Mage::getStoreConfig("joomlart_jmadvancesearch/joomlart_jmadvancesearch/maxnumbers");
    	if (isset($max)&&$max){
    		$collections->setPage(1, $max);
    	}
    	$collections->load();
    	
    	$callback = $this->getRequest()->getParam('callback');
    	$result['query']= $this->getRequest()->getParam('query');
    	foreach($collections as $collection){
    		$result['suggestions'][]=$collection->getName();
    	}
    	
    	echo $callback. "(".json_encode($result).");";
    }
}