<?php

class JoomlArt_JmProducts_ViewallController extends Mage_Core_Controller_Front_Action{
	
	public function IndexAction() {
       $this->getRequest()->setParam('viewall',true);
	   $this->loadLayout();
	   $viewall = $this->getLayout()->getBlock('viewall.jmproducts.list');
       $viewall->setData("viewall",true);
       $this->renderLayout(); 
	  
    }
    

}

?>