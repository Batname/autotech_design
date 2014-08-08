<?php
class Wavethemes_Jmbasetheme_Helper_Data extends Mage_Core_Helper_Abstract
{
        
        var $defaultstore_Code;
        public function __construct(){
            $currentstore =  Mage::app()->getRequest()->getParam("store");
            $defaultstore = Mage::app()->getWebsite(true)->getDefaultStore()->getCode();
        	$this->defaultstore_Code = $currentstore?$currentstore:$defaultstore;
        }
		public function getcssFolder(){
		
		    $default = Mage::getStoreConfig('design/theme/default',$this->defaultstore_Code)?Mage::getStoreConfig('design/theme/default',$this->defaultstore_Code):"default";

		    $skinfolder =  Mage::getBaseDir("skin").DS."frontend".DS."default".DS.$default;
			if(!is_dir($skinfolder.DS."wavethemes")){
			   mkdir($skinfolder.DS."wavethemes");
			   chmod($skinfolder.DS."wavethemes",0755);
			   mkdir($skinfolder.DS."wavethemes".DS."jmbasetheme");
			   chmod($skinfolder.DS."wavethemes".DS."jmbasetheme",0755);
			   mkdir($skinfolder.DS."wavethemes".DS."jmbasetheme".DS."css");
			   chmod($skinfolder.DS."wavethemes".DS."jmbasetheme".DS."css",0755);
			}
			$cssfolder = $skinfolder.DS."wavethemes".DS."jmbasetheme".DS."css".DS;
			return $cssfolder;
		}

         
		public function getskinProfileFolder($storecode = ""){

		    $storecode = $storecode?$storecode:$this->defaultstore_Code;
		    $default = Mage::getStoreConfig('design/theme/default',$storecode)?Mage::getStoreConfig('design/theme/default',$storecode):"default";
		    $skinfolder =  Mage::getBaseDir("skin").DS."frontend".DS."default".DS.$default;
			if(!is_dir($skinfolder.DS."wavethemes")){
			   mkdir($skinfolder.DS."wavethemes");
			   chmod($skinfolder.DS."wavethemes",0755);
			   mkdir($skinfolder.DS."wavethemes".DS."jmbasetheme");
			   chmod($skinfolder.DS."wavethemes".DS."jmbasetheme",0755);
			   mkdir($skinfolder.DS."wavethemes".DS."jmbasetheme".DS."profiles");
			   chmod($skinfolder.DS."wavethemes".DS."jmbasetheme".DS."profiles",0755);
			}
			$skinfolder = $skinfolder.DS."wavethemes".DS."jmbasetheme".DS."profiles".DS;
			return $skinfolder;
		}

       
		public function getprofileFolder($storecode = ""){
            
		    $storecode = $storecode?$storecode:$this->defaultstore_Code;
		    $default = Mage::getStoreConfig('design/theme/default',$storecode)?Mage::getStoreConfig('design/theme/default',$storecode):"default";

		    $themefolder =  Mage::getBaseDir("design").DS."frontend".DS."default".DS.$default;
			if(!is_dir($themefolder.DS."profiles")){
			   mkdir($themefolder.DS."profiles");
			   chmod($themefolder.DS."profiles",0755);
			}
			if(is_dir($themefolder.DS."profiles") && !is_dir($themefolder.DS."profiles".DS."core")){
			   mkdir($themefolder.DS."profiles".DS."core");
			   chmod($themefolder.DS."profiles".DS."core",0755);	
			}
			if(is_dir($themefolder.DS."profiles") && !is_dir($themefolder.DS."profiles".DS."local")){

			   mkdir($themefolder.DS."profiles".DS."local");
			   chmod($themefolder.DS."profiles".DS."local",0755);	
			}
			$profilesfolder = $themefolder.DS."profiles".DS;
			return $profilesfolder;
		}
		
		public function getactiveprofile(){

             $activeprofile = Mage::helper("jmbasetheme")->getprofile();
		     $defaultheme = Mage::getStoreConfig('design/theme/default',$this->defaultstore_Code)?Mage::getStoreConfig('design/theme/default',$this->defaultstore_Code):"default";
		       
		     if($activeprofile == "default"){
		        if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS."default.ini")){
		           $baseconfig = parse_ini_file(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS."default.ini");
		        }else if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS."default.ini")){
		           $baseconfig = parse_ini_file(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS."default.ini");
		        }else {
		           $baseconfig = Mage::getStoreConfig("wavethemes_jmbasetheme/jmbasethemedefault");
		        }
		     }else{
		        if(file_exists(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$activeprofile.".ini")){ 
		          $baseconfig = parse_ini_file(Mage::helper("jmbasetheme")->getprofileFolder().DS."local".DS.$activeprofile.".ini");
		        }else{
		          $baseconfig = parse_ini_file(Mage::helper("jmbasetheme")->getprofileFolder().DS."core".DS.$activeprofile.".ini");
		        }
		       
		    }

		    //Get the correct device fields
		    $devicedetect = Mage::helper ('jmbasetheme/mobiledetect');
		    if($devicedetect->isTablet()){
		         $baseconfig["productlistdeslenght"] = isset($baseconfig["productlistdeslenghttablet"])&&$baseconfig["productlistdeslenghttablet"]?$baseconfig["productlistdeslenghttablet"]:$baseconfig["productlistdeslenght"];
                 $baseconfig["showlabel"] = isset($baseconfig["showlabeltablet"])&&$baseconfig["showlabeltablet"]?$baseconfig["showlabeltablet"]:$baseconfig["showlabel"];
                 $baseconfig["productgridnumbercolumn"] = isset($baseconfig["productgridnumbercolumntablet"])&&$baseconfig["productgridnumbercolumntablet"]?$baseconfig["productgridnumbercolumntablet"]:$baseconfig["productgridnumbercolumn"];
                 $baseconfig["quanlityperpage"] = isset($baseconfig["quanlityperpagetablet"])&&$baseconfig["quanlityperpagetablet"]?$baseconfig["quanlityperpagetablet"]:"";
                 $baseconfig["productlimageheight"] = isset($baseconfig["productlimageheighttablet"])&&$baseconfig["productlimageheighttablet"]?$baseconfig["productlimageheighttablet"]:$baseconfig["productlimageheight"];
                 $baseconfig["productlimageheightportrait"] = isset($baseconfig["productlimageheighttabletportrait"])&&$baseconfig["productlimageheighttabletportrait"]?$baseconfig["productlimageheighttabletportrait"]:$baseconfig["productlimageheight"];
                 if($baseconfig["productlimageheightportrait"] > $baseconfig["productlimageheight"]) $baseconfig["productlimageheight"] = $baseconfig["productlimageheightportrait"];
                 $baseconfig["productlimagewidth"] = isset($baseconfig["productlimagewidthtablet"])&&$baseconfig["productlimagewidthtablet"]?$baseconfig["productlimagewidthtablet"]:$baseconfig["productlimagewidth"];
		         $baseconfig["productlimagewidthportrait"] = isset($baseconfig["productlimagewidthtabletportrait"])&&$baseconfig["productlimagewidthtabletportrait"]?$baseconfig["productlimagewidthtabletportrait"]:$baseconfig["productlimagewidth"];
                 if($baseconfig["productlimagewidthportrait"] > $baseconfig["productlimagewidth"]) $baseconfig["productlimagewidth"] = $baseconfig["productlimagewidthportrait"];
                 
		    }else if($devicedetect->isMobile()){
		         $baseconfig["productlistdeslenght"] = isset($baseconfig["productlistdeslenghttmobile"])&&$baseconfig["productlistdeslenghttmobile"]?$baseconfig["productlistdeslenghtmobile"]:$baseconfig["productlistdeslenght"];
                 $baseconfig["showlabel"] = isset($baseconfig["showlabelmobile"])&&$baseconfig["showlabelmobile"]?$baseconfig["showlabelmobile"]:$baseconfig["showlabel"];
                 $baseconfig["productgridnumbercolumn"] = isset($baseconfig["productgridnumbercolumnmobile"])&&$baseconfig["productgridnumbercolumnmobile"]?$baseconfig["productgridnumbercolumnmobile"]:$baseconfig["productgridnumbercolumn"];
                 $baseconfig["quanlityperpage"] = isset($baseconfig["quanlityperpagemobile"])&&$baseconfig["quanlityperpagemobile"]?$baseconfig["quanlityperpagemobile"]:"";
                 $baseconfig["productlimageheight"] = isset($baseconfig["productlimageheightmobile"])&&$baseconfig["productlimageheightmobile"]?$baseconfig["productlimageheightmobile"]:$baseconfig["productlimageheight"];
                 $baseconfig["productlimagewidth"] = isset($baseconfig["productlimagewidthmobile"])&&$baseconfig["productlimagewidthmobile"]?$baseconfig["productlimagewidthmobile"]:$baseconfig["productlimagewidth"];
		    }
		    return  $baseconfig;

		}
		public function gettheme(){
        	 $theme = Mage::getStoreConfig('design/theme/default',$this->defaultstore_Code)?Mage::getStoreConfig('design/theme/default',$this->defaultstore_Code):"default";
        	 return trim($theme);
        }
        

        public function getprofile(){

            //get store code from current url 
        	$stores = Mage::app()->getStores();
			foreach($stores as $store){
				if(strpos(Mage::helper('core/url')->getCurrentUrl(),$store->getBaseUrl()) !== False){
		            $storecode =  $store->getCode();
				}  
				
			}
			
            $storecode = isset($storecode)&&$storecode?$storecode:$this->defaultstore_Code;
        	$defaultprofile = Mage::getStoreConfig("wavethemes_jmbasetheme/jmbasethemegeneral/profile",$storecode);
        	$Profiles = array_keys(Mage::helper("jmbasetheme")->getProfiles());
        	$exp = time() + 60*60*24*355;
        	if(($color = Mage::app()->getRequest()->getParam("jmcolor")) &&  in_array($color, $Profiles)){
                $defaultprofile = $color;
                @setcookie(Mage::helper("jmbasetheme")->gettheme()."_color",$color,$exp,"/");
			}else if(isset($_COOKIE[Mage::helper("jmbasetheme")->gettheme()."_color"])){
                $defaultprofile = $_COOKIE[Mage::helper("jmbasetheme")->gettheme()."_color"];
			}
			return $defaultprofile;
        }


		public function getProfiles(){
	
			$profiles = array();
			$profilecores = array();
			$profilelocals = array();

			$profiles["default"]  = new stdclass();
			$profilesfolder = $this->getprofileFolder();

			$filecores = $this->files($profilesfolder.DS."core",'\.ini');

			if($filecores){
			  foreach($filecores as $file){
			     $profilecores[strtolower(substr($file,0, -4))] = parse_ini_file($profilesfolder.DS."core".DS.$file);
			  } 
			}
			$filelocals = $this->files($profilesfolder.DS."local",'\.ini');
			if($filelocals){
			  foreach($filelocals as $file){
			     $profilelocals[strtolower(substr($file,0, -4))] = parse_ini_file($profilesfolder.DS."local".DS.$file);
			  } 
			}

			$profiles = array_merge($profilecores,$profilelocals);
			
			if(empty($profiles)){
				$profiles["default"]  = new stdclass();
			}
			foreach($profiles as $profile => $settings){
				if(file_exists($profilesfolder.DS."core".DS.$profile.".ini")){
					$profiles[$profile]["iscore"] = true;
				}

			}
           
			return $profiles;
		}
		
		
		
		public function files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
		{
			// Initialize variables
			$arr = array();
	
			
			// Is the path a folder?
			if (!is_dir($path)) {
				return false;
			}
	
			// read the source directory
			$handle = opendir($path);
			while (($file = readdir($handle)) !== false)
			{
				if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
					$dir = $path . DS . $file;
					$isDir = is_dir($dir);
					if ($isDir) {
						if ($recurse) {
							if (is_integer($recurse)) {
								$arr2 = $this->files($dir, $filter, $recurse - 1, $fullpath);
							} else {
								$arr2 = $this->files($dir, $filter, $recurse, $fullpath);
							}
							
							$arr = array_merge($arr, $arr2);
						}
					} else {
						if (preg_match("/$filter/", $file)) {
							if ($fullpath) {
								$arr[] = $path . DS . $file;
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
		
		function write_ini_file($assoc_arr,$path) { 
			
			$content = "\n"; 
			foreach ($assoc_arr as $key=>$elem) { 
				if(is_array($elem)) 
				{ 
					for($i=0;$i<count($elem);$i++) 
					{ 
						$content .= $key."[] = \"".$elem[$i]."\"\r\n"; 
					} 
				} 
				else if($elem=="") $content .= $key." = \r\n"; 
				else $content .= $key." = \"".$elem."\"\r\n"; 
			} 
		    
			if (!$handle = fopen($path, 'w')) { 
				return false; 
			} 
			if (!fwrite($handle, $content)) {
				return false; 
			} 
			fclose($handle); 
			return true; 
		}
		
		
		function saveProfile($pname,$assoc_arr,$storecode = ""){
		    return $this->write_ini_file($assoc_arr,$this->getprofileFolder($storecode)."local".DS.$pname);
		}
		
		function writeTofile($content,$path){
		    if (!$handle = fopen($path, 'w')) { 
				return false; 
			} 
			if (!fwrite($handle, $content)) { 
				return false; 
			} 
			fclose($handle); 
			return true; 
		}
		
}
	 