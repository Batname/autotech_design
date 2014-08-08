<?php
   class Wavethemes_Jmbasetheme_Block_Adminhtml_system_config_form_colorpicker extends Mage_Adminhtml_Block_System_Config_Form_Field
   {
         protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
         {
		    // Get the default HTML for this option
            $output = parent::_getElementHtml($element);
			$output .= '<span  id="icp_'.$element->getHtmlId().'" class="mColorPickerTrigger" style="cursor:pointer;"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="http://demo.diymage.com/skin/adminhtml/default/default/diy/images/color.png"></span>';
			
			if ( !Mage::registry('rightcolor') ) {
			    $output .=  '
				<script type="text/javascript" src="'.$this->getJsUrl('joomlart/jmbasetheme/jquery1.6.4.min.js').'"></script>
                <script type="text/javascript" src="'.$this->getJsUrl('joomlart/jmbasetheme/mColorpicker.min.js').'"></script>
				 <script type="text/javascript">
						jQuery.noConflict();
						jQuery.fn.mColorPicker.init.replace = false;
						jQuery.fn.mColorPicker.init.enhancedSwatches = false;
						jQuery.fn.mColorPicker.init.allowTransparency = true;
						jQuery.fn.mColorPicker.init.showLogo = false;
						jQuery.fn.mColorPicker.defaults.imageFolder = "'.$this->getJsUrl('joomlart/jmbasetheme/mColorPicker/').'";
                </script>
				';
				 Mage::register('rightcolor', 1);
		    }
			$output .= '
				<script type="text/javascript">
					jQuery("#'.$element->getHtmlId().'").width("200px").attr("data-hex", true).mColorPicker({swatches: [
              "#9a1212",
              "#93ad2a",
              "#00ff00",
              "#00ffff",
              "#0000ff",
              "#ff00ff",
              "#ff0000",
              "#4c2b11",
              "#3b3b3b",
              "#000000"
            ]});
				</script>
				';
			return $output;
         }
   }

?>