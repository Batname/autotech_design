<?php

class Wavethemes_Jmbasetheme_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Frontend basetheme"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("frontend basetheme", array(
                "label" => $this->__("Frontend basetheme"),
                "title" => $this->__("Frontend basetheme")
		   ));

      $this->renderLayout(); 
	  
    }
	
	public function createProfileAction(){
	
	       $results = array(); 
		   $requests =  $this->getRequest()->getParams();
		   $profilename = $requests["profile"];
		   $settings = $requests["settings"];
		   if(Mage::helper("jmbasetheme")->saveProfile($profilename.".ini",$settings)){
		     $result['successful'] = "Profile ".$profilename." was successfully created!";
			 $result['profile'] = $profilename;
			 $result['settings'] = $settings;
			 $result['type'] = "new";
             //clone the core profile's params

             //clone the profile's params 
		    if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS."core.xml")){
		  
		      $xmlcontent = file_get_contents(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS."core.xml");
			  $xmlcontent = str_replace("jmbasethemecore","jmbasetheme".$profilename,$xmlcontent);
			  
			  $xmlcontent = preg_replace("/settings\s*for\s*core\s*profile/","settings for ".$profilename." profile",$xmlcontent);
			  
			  if(!Mage::helper("jmbasetheme")->writeTofile($xmlcontent,Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$profilename.".xml")){
			    $result['error']  = "Could not save the ".$profilename." profile settings !";
			  }
		    }
		     //create new skin profile folder if it was not there
             if(!is_dir(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename)){
           	   mkdir(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename);
           	   chmod(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename,0755);
             }
			 //Clone the frontend css file from core  
			 if(file_exists(Mage::helper("jmbasetheme")->getskinProfileFolder().DS."core".DS."core".".css.php")){
	              $csscontent = trim(file_get_contents(Mage::helper("jmbasetheme")->getskinProfileFolder().DS."core".DS."core".".css.php"));
	              Mage::helper("jmbasetheme")->writeTofile($csscontent,Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename.DS.$profilename.".css.php");
	              chmod(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename.DS.$profilename.".css.php",0755);
			 }
			 //clone the profile image folder
             if(is_dir(Mage::helper("jmbasetheme")->getskinProfileFolder().DS."core".DS."images")){
           	  $src = Mage::helper("jmbasetheme")->getskinProfileFolder().DS."core".DS."images";
           	  $dst = Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename.DS."images";
              $this->rcopy($src,$dst);
		     } 
		   }else{
		     $result['error']  = "An error occurred while Creating the profile !";
 		   }
		   echo json_encode($result);
	}

	public function rcopy($src, $dst) {
	  if (file_exists($dst)) rmdir($dst);
	  if (is_dir($src)) {
	    mkdir($dst);
	    $files = scandir($src);
	    foreach ($files as $file)
	    if ($file != "." && $file != "..") $this->rcopy("$src/$file", "$dst/$file");
	  }
	  else if (file_exists($src)) copy($src, $dst);
    }

	public function cloneProfileAction() {
	       $results = array(); 
	       $requests =  $this->getRequest()->getParams();
		   $oldprofile = $requests["oldprofile"];
		   $profilename = $requests["profile"];
		   $settings = $requests["settings"];
		   
		   //clone the profile's params 
		   if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS.$oldprofile.".xml")){
		  
		      $xmlcontent = file_get_contents(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS.$oldprofile.".xml");
			  $xmlcontent = str_replace("jmbasetheme".$oldprofile,"jmbasetheme".$profilename,$xmlcontent);
			  
			  $xmlcontent = preg_replace("/settings\s*for\s*".$oldprofile."\s*profile/","settings for ".$profilename." profile",$xmlcontent);
			  
			  if(!Mage::helper("jmbasetheme")->writeTofile($xmlcontent,Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$profilename.".xml")){
			    $result['error']  = "Could not save the ".$profilename." profile settings !";
			  }
		   }else if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$oldprofile.".xml")){
              $xmlcontent = file_get_contents(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$oldprofile.".xml");
			  $xmlcontent = str_replace("jmbasetheme".$oldprofile,"jmbasetheme".$profilename,$xmlcontent);
			  
			  $xmlcontent = preg_replace("/settings\s*for\s*".$oldprofile."\s*profile/","settings for ".$profilename." profile",$xmlcontent);
			  
			  if(!Mage::helper("jmbasetheme")->writeTofile($xmlcontent,Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$profilename.".xml")){
			    $result['error']  = "Could not save the ".$profilename." profile settings !";
			  }

		   }
           //create new skin profile folder if it was not there
           if(!is_dir(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename)){
           	  mkdir(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename);
           }

		   //Clone the frontend css file $profile.css.php
		   if(file_exists(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$oldprofile.DS.$oldprofile.".css.php")){
              $csscontent = file_get_contents(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$oldprofile.DS.$oldprofile.".css.php");
              Mage::helper("jmbasetheme")->writeTofile($csscontent,Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename.DS.$profilename.".css.php");
		   }
           //clone the profile image folder
           if(is_dir(Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$oldprofile.DS."images")){
           	  $src = Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$oldprofile.DS."images";
           	  $dst = Mage::helper("jmbasetheme")->getskinProfileFolder().DS.$profilename.DS."images";
              $this->rcopy($src,$dst);
		   } 

           //clone the setting values 
		   if(Mage::helper("jmbasetheme")->saveProfile($profilename.".ini",$settings)){
		      $result['successful'] = "Profile ".$profilename." was successfully cloned!";
			  $result['profile'] = $profilename;
			  $result['oldprofile'] = $oldprofile;
			  $result['settings'] = $settings;
			  $result['type'] = "clone";
		   }else{
		     $result['error']  = "An error occurred while Creating the profile !";
 		   }	
		   echo json_encode($result);	   
	}
   
	public function saveProfileAction(){
           $results = array(); 
	       $requests =  $this->getRequest()->getParams();
		   $profilename = $requests["profile"];
		   
		   //check to see if this is a core profile
           if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS.$profilename.".ini")){
           	  $iscore = true;
           }    
		   if($requests["storecode"]){
               $storecode = $requests["storecode"];
		   }

		   $settings = $requests["settings"];

           if($settings["deleteimages"] && $settings["deleteimages"] !== "" && !$iscore){
           	 $deleteimages = explode(",",$settings["deleteimages"]);
           	 foreach($deleteimages as $image){
           	 	if(file_exists(Mage::helper("jmbasetheme")->getskinProfileFolder($storecode).DS.$profilename.DS."images".DS.$image)){
                   @unlink(Mage::helper("jmbasetheme")->getskinProfileFolder($storecode).DS.$profilename.DS."images".DS.$image);
           	 	}
           	 }
           }
		   if(Mage::helper("jmbasetheme")->saveProfile($profilename.".ini",$settings,$storecode)){
			 $result['profile'] = $profilename;
			 $result['settings'] = $settings;
			 $result['type'] = "saveProfile";
		   }else{
		     $result['error']  = "An error occurred while Creating the profile !";
 		   }
           echo json_encode($result);	 
	}


	public function restoreProfileAction(){
		   $results = array(); 
	       $requests =  $this->getRequest()->getParams();
	       $profilename = $requests["profile"];
	       if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS.$profilename.".ini")){
              $settings = parse_ini_file(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS.$profilename.".ini");
              $settings["iscore"] = true;
              Mage::helper("jmbasetheme")->saveProfile($profilename.".ini",$settings);
              $result['successful'] = "Profile ".$profilename." was successfully Restored to default!";
              $result['profile'] = $profilename;
			  $result['settings'] = $settings;
			  $result['type'] = "restore";
	       }else{
	       	  $result['error']  = "This is not a core profile so you can't restore it !";
	       }
 
           echo json_encode($result);
	}

	public function deleteProfileAction(){
		   $results = array(); 
	       $requests =  $this->getRequest()->getParams();
	       $profilename = $requests["profile"];
	       if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$profilename.".ini")){
              @unlink(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$profilename.".ini");
	          if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$profilename.".xml")){
	             @unlink(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$profilename.".xml");
	          }
	          Mage::getConfig()->saveConfig("wavethemes_jmbasetheme/jmbasethemegeneral/profile","default");
	          $result['successful'] = "Profile ".$profilename." was successfully Deleted !";
              $result['profile'] = $profilename;
			  $result['type'] = "delete";
	       }else{
	       	  $result['error']  = "The profile does not exists !";
	       }
 
           echo json_encode($result);
	}
	
}