<?php
   
       class JoomlArt_JmProducts_Block_Adminhtml_system_config_form_settings extends Mage_Adminhtml_Block_System_Config_Form_Field
       { 
	        protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
            {
			  $output .=  '
				<script type="text/javascript" src="'.$this->getJsUrl('joomlart/jmproducts/jquery1.6.4.min.js').'"></script>
                <script type="text/javascript" src="'.$this->getJsUrl('joomlart/jmproducts/jmproduct.js').'"></script>';

			        return $output;
            }
	   } 
