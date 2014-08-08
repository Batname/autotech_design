<?php
    class JoomlArt_JmProducts_Model_Unit extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
        public function getAllOptions()
        {
            if (!$this->_options) {
                $this->_options = array(
                   
                    array(
                        'value' => '0',
                        'label' => 'No',
                    ),
                    array(
                        'value' => '1',
                        'label' => 'Yes',
                    )
                );
            }
            return $this->_options;
        }
    }





?>