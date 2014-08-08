<?php

    class Wavethemes_Jmbasetheme_Model_Observer
	{
		
	    public function saveprofile()
        {
		     $storeini = array();
		     $profile = Mage::getStoreConfig("wavethemes_jmbasetheme/jmbasethemegeneral/profile");
			 $settings = Mage::getStoreConfig("wavethemes_jmbasetheme");
			 $storeini = $settings["jmbasethemedefault"];
			 if(is_array($settings["jmbasetheme".$profile])){
			   $storeini = array_merge($settings["jmbasethemedefault"],$settings["jmbasetheme".$profile]);
			 }
			 //echo "<pre>";
			 //print_r($storeini);die();
		    // Mage::helper("jmbasetheme")->saveProfile($profile.".ini",$storeini); 
		    
        } 
		
		public function Extendconfig($observer){
		    $profiles = array_keys(Mage::helper("jmbasetheme")->getProfiles());

             $mergeobject = new Mage_Core_Model_Config_Base();
            

			 foreach($profiles as $profile){
				 if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS.$profile.".xml")){
				    $mergeobject->loadFile(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS.$profile.".xml");
				 }else{
                  	$mergeobject->loadFile(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$profile.".xml");
				 }
				 $observer->config->extend($mergeobject, false);
			 }
			 
			 if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS."core.xml")){
                  $mergeobject->loadFile(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS."core.xml");
                   $observer->config->extend($mergeobject, false);
			 }
			  //extend tablet settings
             $mergeobject->loadFile(Mage::getModuleDir('etc', 'Wavethemes_Jmbasetheme')."/device.xml");
             $observer->config->extend($mergeobject, false);
              //extend mobile settings
             $mergeobject->loadFile(Mage::getModuleDir('etc', 'Wavethemes_Jmbasetheme')."/mobile.xml");
             $observer->config->extend($mergeobject, false);
		}
		
		public function changeBody($response){
			 $activeprofile = Mage::helper("jmbasetheme")->getprofile();
			 $body =  $response["response"]->getBody();
			 $addlinks = '<link type="text/css" rel="stylesheet" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/wavethemes/jmbasetheme/css/settings.css.php?profile='.$activeprofile.'" />';
             //add the active profile css.php file
			 if(file_exists(Mage::helper("jmbasetheme")->getskinProfileFolder().$activeprofile.DS.$activeprofile.".css.php")){
			 	$activeprofilecontent = file_get_contents(Mage::helper("jmbasetheme")->getskinProfileFolder().$activeprofile.DS.$activeprofile.".css.php");
                $activeprofilecontent = trim($activeprofilecontent);
                if(!empty($activeprofilecontent)){
                 Mage::helper("jmbasetheme")->writeTofile(trim($activeprofilecontent),Mage::helper("jmbasetheme")->getskinProfileFolder().$activeprofile.DS.$activeprofile.".css.php");
			 	}
			 	$link = str_replace(Mage::getBaseDir("skin"),"", Mage::helper("jmbasetheme")->getskinProfileFolder().$activeprofile.DS.$activeprofile.".css.php");
			 	$link = str_replace("\\", "/", $link);
			 	$addlinks .= '<link type="text/css" rel="stylesheet" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).$link.'" />';
			 }
			 $body = str_replace("</head>", $addlinks.'</head>', $body);
			 $response["response"]->setBody($body);
    	}

    	public function resettoolbar($observer){

            $toolbar = $observer['layout']->getBlock("product_list_toolbar");
            $devicedetect = Mage::helper ('jmbasetheme/mobiledetect');
            if(!Mage::app()->getStore()->isAdmin()){
	            $baseconfig =  Mage::helper("jmbasetheme")->getactiveprofile();
	            if(Mage::registry('current_category') && $toolbar &&  $devicedetect->isMobile() && $baseconfig['quanlityperpage'] != ""){
	                $toolbar->addPagerLimit("grid",$baseconfig["quanlityperpage"]);
	                $toolbar->addPagerLimit("list",8);	
	            }
            }
           
    	}
	
	}
	
	

