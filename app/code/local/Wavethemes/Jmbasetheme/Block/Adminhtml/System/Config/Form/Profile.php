<?php
   
       class Wavethemes_Jmbasetheme_Block_Adminhtml_system_config_form_profile extends Mage_Adminhtml_Block_System_Config_Form_Field
       { 
          protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
            {
            
               if(Mage::getStoreConfig("web/secure/use_in_adminhtml")){
                                  $baseurl = Mage::getStoreConfig("web/secure/base_url");
               }else{
                          $baseurl = Mage::getBaseUrl();  
               }
              $baseurl = str_replace("admin/","", $baseurl);
              $storecode = Mage::app()->getWebsite(true)->getDefaultStore()->getCode();
              $defaulttheme = Mage::getStoreConfig('design/theme/default',$storecode)?Mage::getStoreConfig('design/theme/default',$storecode):"default"; 
              $output = '<script type="text/javascript">var defaulttheme = "'.$defaulttheme.'",baseurl ="'.Mage::getBaseUrl().'"  </script>';  
              $output .= '<script type="text/javascript" src="'.str_replace("index.php","",$baseurl).'/js/joomlart/jmbasetheme/jquery1.6.4.min.js"> </script>'.
              '<script type="text/javascript" src="'.str_replace("index.php","",$baseurl).'/js/joomlart/jmbasetheme/jmbasetheme.js"></script>';  
        $output .= '<button  style="float:left"  type="button" id="clone-profile"  name="Clone"  >Clone this profile</button><button  style="float:left"  type="button" id="restore-profile"  name="Restore"  >Restore this profile</button>
        <button  style="float:left"  type="button" id="delete-profile"  name="Delete"  >Delete this profile</button>';
              $output .= '<script type="text/javascript">';
        $profiles = Mage::helper("jmbasetheme")->getProfiles();
        $output .= 'var profiles = '.json_encode($profiles).';';
        $output .=  '</script>';
        return $output;
            }
     } 
