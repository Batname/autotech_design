<?php


class Wavethemes_Jmbasetheme_Adminhtml_JmbasethemebackendController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
    {
	  
       $this->loadLayout("catalog_category_default");
	   
	   $xml = $this->getLayout()->getNode()->asNiceXml();
	   $xml = preg_replace("/<update handle=([^\/]*)\/>/","",$xml);
	   $xmlObj = new Varien_Simplexml_Config($xml);
	   $xmlData = $xmlObj->getNode();
	   echo "<pre>";
       print_r(json_decode(json_encode((array)simplexml_load_string($xml)),1)); die();	  
	   $this->_title($this->__("Backend Page Title"));
	   $this->renderLayout();
    }
	
	public function saveAction(){
	   $filepath = Mage::getBaseDir("skin").DS."frontend".DS."default".DS."default".DS."css".DS.'option.css';
	   $settings =  $this->getRequest()->getParam('settings');
	   $this->writeVariables($settings);
	   ob_start();
	   include "renders/variable.php";
	   include "renders/css.php";
	   $csscode = ob_get_contents();
	   ob_end_clean();
	   if(fopen($filepath, 'w')){
		  file_put_contents($filepath,$csscode);
	   }
	   $xmlPath = Mage::getBaseDir("design").DS."frontend".DS."base".DS."default".DS."layout".DS.'jmbasetheme.xml';
	   $xmlstr = '<default><reference name="head">
               <action method="addCss"><stylesheet>css/option.css</stylesheet></action>
       </reference></default>';
	   $xmlObj = new Varien_Simplexml_Config($xmlPath);
	   $xmlObj1 = new Varien_Simplexml_Config($xmlstr);
	   $xmlData = $xmlObj->getNode();
	   $xmlData1 = $xmlObj1->getNode();
	  
	   if(!$xmlData->descend("default/reference@name=head")){
	     $reference = $xmlData->appendChild($xmlData1);
		 file_put_contents($xmlPath,$xmlData->asNiceXml());
	   }else{
	     $oNode = dom_import_simplexml($xmlData->default);
		 $oNode->parentNode->removeChild($oNode);
		 file_put_contents($xmlPath,$xmlData->asNiceXml());
	   }
	   $this->_redirect('*/*/');
	   
	}
	
	public function writeVariables($settings){
	    $textoutput = "<?php \n";
		foreach($settings as $ksetting => $setting){
		   $textoutput .=  "\$settings[$ksetting] = \"$setting\" \n";
		}
		if(fopen("variable.php",'w')){
		   file_put_contents(Mage::getBaseDir("code").DS."local".DS."Wavethemes".DS."Jmbasetheme".DS."controllers".DS."Adminhtml".DS."variable.php",$textoutput);
		}
	}
	
	
}