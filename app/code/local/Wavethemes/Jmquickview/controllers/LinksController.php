<?php

class Wavethemes_Jmquickview_LinksController extends Mage_Core_Controller_Front_Action {



   public function IndexAction()
   {
         $this->loadLayout();
         $this->renderLayout();
   }
   
   public function updatecartAction()
   {
         $this->loadLayout();
         $this->renderLayout();
   }
   public function totalAction(){

         if(Mage::getSingleton('checkout/session')->getQuote()->getSubtotal() > 0) { 
            echo '$'.number_format(Mage::getSingleton('checkout/session')->getQuote()->getSubtotal(),2);
            echo ' - '.Mage::getSingleton('checkout/session')->getQuote()->getItemsSummaryQty().' items';
         } else {
            echo $this->__('No items');
         } 

   }
   public function sumAction(){
         echo $this->__('Shopping bag')." ( <a class='gotocart' href='".Mage::getUrl("checkout/cart")."' >";
         if(Mage::getSingleton('checkout/session')->getQuote()->getSubtotal() > 0) { 
            echo Mage::getSingleton('checkout/session')->getQuote()->getItemsSummaryQty().$this->__(' items');
         } else {
            echo $this->__('0 items');
         }
         echo "</a> )"; 

   }
   	public function sumjsonAction(){
   		$result['cart_url']= Mage::getUrl("checkout/cart");
   		$result['sum_items']= 0;
   		if(Mage::getSingleton('checkout/session')->getQuote()->getSubtotal() > 0) {
   			$result['sum_items']= Mage::getSingleton('checkout/session')->getQuote()->getItemsSummaryQty();
   		}
   		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
   	}

}