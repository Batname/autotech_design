<?php

    class Wavethemes_Jmbasetheme_Model_Config_Profile
	{
		
	    public function toOptionArray()
        {
		    $profiles = Mage::helper("jmbasetheme")->getProfiles();
			$profiles = array_keys($profiles);
			$options = array();
			foreach ($profiles as $f ){
				$options[] = array(
					'value' => $f,
					'label' => $f,
				);
			}
			
			return $options;
        } 
	
	}
	
	
?>	
