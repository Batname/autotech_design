<?php
class Magestore_Instantsearch_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	
		$keyword = urldecode($this->getRequest()->getParam('keyword'));
		$q = urldecode($this->getRequest()->getParam('q'));
		
		if($q)
		{
			$keyword = $q;
		}
		Mage::register('keyword',$keyword);
		
		$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function searchAction()
    {
		if(!Mage::helper('magenotification')->checkLicenseKey('Instantsearch')){
			return;
		}	
		$keyword = $this->getRequest()->getPost('keyword');
			
		$products = Mage::helper("instantsearch")->getSearchProduct($keyword);
		$_coreHelper = Mage::helper('core');
		
		$result = array();
		$items = array();
		$thumb_width = Mage::getStoreConfig('instantsearch/general/thumb_image_width');
		if(count($products))
		{
			foreach($products as $productId)
			{
				$product = Mage::getModel("catalog/product")->load($productId);
				$img = Mage::helper('catalog/image')->init($product, 'image')->resize($thumb_width);
				$img = $img->__toString();
				$price = $_coreHelper->currency($product->getPrice(),true,false);
				$product_url = $product->getProductUrl();
				$items[] = array("name"=>$product->getName(),"id"=>$product->getId(),"image"=>$img,"price"=>$price,"url"=>$product_url);
				
			}
			$result['products'] = $items;
		}
    	$this->getResponse()->setBody(Zend_Json::encode($result));		
    }
	
	public function loadproductAction()
    {
		$_coreHelper = Mage::helper('core');
		$cart_helper = Mage::helper('checkout/cart');
		
		$product_helper = Mage::helper("catalog/product");
		$id = $this->getRequest()->getParam('id');				
		$product = Mage::getModel('catalog/product')->load($id);
		$img = Mage::helper('catalog/image')->init($product, 'image')->resize(220);
		$product_url = $product->getProductUrl();
		$product_name = $product->getName();
		
		$html ="";
		$html .= "<form id='productAddToCartForm' name='productAddToCartForm' action='". $cart_helper->getAddUrl($product,array()). "' method='post'>";
		
		$html .= "<div id='mainProduct'>
			<div id='product_img_box'><a href='".$product_url."' title='".$product_name."' target='_blank'><img src='". $img ."' title='".$product_name."'></a></div>
		<div id='product_information'>
			<h3 title='".$product->getName()."'>".$product->getName()."</h3>
			<div class='product_price'>".$_coreHelper->currency($product->getPrice(),true,false). "</div>";
		
		if($product->isSaleable())
		{
			$html .= "<fieldset class='add-to-cart-box'>
	        <legend>" . $product_helper->__('Add Items to Cart') . "</legend>";
			
	        if(!$product->isGrouped())
			{
		       $html .= " <span class='qty-box'><label for='qty'>". $product_helper->__('Qty') .":</label>
		        <input name='qty' type='text' class='input-text qty' value='1' id='qty' maxlength='12' /></span>";
	        }
	        $html .=" <button type='button' class='button' onclick='javascript:document.productAddToCartForm.submit()'><span>". $product_helper->__('Add to Cart') ."</span></button>";
			$html .="</fieldset>";
		}
		
			
		$html .="<br><div id='description'><h4>".Mage::helper("catalog/product")->__("Quick Overview")."&nbsp;&nbsp;<a href='".$product_url."'  target='_blank'>".$product_helper->__('View Detail')."</a></h4>"
					. $product->getShortDescription()			
		      ."</div>"		
		."</div>
		  </div></form>";
		$this->getResponse()->setHeader('Content-type', 'application/x-json');
         $this->getResponse()->setBody($html);		
    }
	public function loadproductviewAction()
    {
		$_coreHelper = Mage::helper('core');
		$cart_helper = Mage::helper('checkout/cart');
		
		$product_helper = Mage::helper("catalog/product");
		$id = $this->getRequest()->getParam('id');				
		$product = Mage::getModel('catalog/product')->load($id);
		$img = Mage::helper('catalog/image')->init($product, 'image')->resize(220);
		$product_url = $product->getProductUrl();
		$product_name = $product->getName();
		
		$html ="";
		$html .= "<form id='productAddToCartForm' name='productAddToCartForm' action='". $cart_helper->getAddUrl($product,array()). "' method='post'>";
		
		$html .= "<div id='mainViewProduct'>
			<div id='product_img_box'><a href='".$product_url."' title='".$product_name."' target='_blank'><img src='". $img ."' title='".$product_name."'></a></div>
		<div id='product_information'>
			<h3 title='".$product->getName()."'>".$product->getName()."</h3>
			<div class='product_price'>".$_coreHelper->currency($product->getPrice(),true,false). "</div>";
		
		if($product->isSaleable())
		{
			$html .= "<fieldset class='add-to-cart-box'>
	        <legend>" . $product_helper->__('Add Items to Cart') . "</legend>";
			
	        if(!$product->isGrouped())
			{
		       $html .= " <span class='qty-box'><label for='qty'>". $product_helper->__('Qty') .":</label>
		        <input name='qty' type='text' class='input-text qty' id='qty' maxlength='12' /></span>";
	        }
	        $html .=" <button type='button' class='button' onclick='javascript:document.productAddToCartForm.submit()'><span>". $product_helper->__('Add to Cart') ."</span></button>";
			$html .="</fieldset>";
		}
		
			
		$html .="<br><div id='description'><h4>".Mage::helper("catalog/product")->__("Quick Overview")."&nbsp;&nbsp;<a href='".$product_url."'  target='_blank'>".$product_helper->__('View Detail')."</a></h4>"
					. $product->getShortDescription()			
		      ."</div>"		
		."</div>
		  </div></form>";
		$this->getResponse()->setHeader('Content-type', 'application/x-json');
         $this->getResponse()->setBody($html);		
    }
}