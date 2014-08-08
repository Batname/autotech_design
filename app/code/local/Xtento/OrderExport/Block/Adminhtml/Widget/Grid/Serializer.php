<?php

if (@class_exists('Mage_Adminhtml_Block_Widget_Grid_Serializer')) {
    if (!@class_exists('Xtento_OrderExport_Block_Adminhtml_Widget_Grid_Serializer')) {
        class Xtento_OrderExport_Block_Adminhtml_Widget_Grid_Serializer extends Mage_Adminhtml_Block_Widget_Grid_Serializer
        {

        }
    }
} else {
    if (!@class_exists('Xtento_OrderExport_Block_Adminhtml_Widget_Grid_Serializer')) {
        class Xtento_OrderExport_Block_Adminhtml_Widget_Grid_Serializer extends Mage_Core_Block_Template
        {

            /**
             * Store grid input names to serialize
             *
             * @var array
             */
            private $_inputsToSerialize = array();

            /**
             * Set serializer template
             *
             * @return Mage_Adminhtml_Block_Widget_Grid_Serializer
             */
            public function _construct()
            {
                parent::_construct();
                $this->setTemplate('xtento/orderexport/widget/grid/serializer.phtml');
                return $this;
            }

            /**
             * Register grid column input name to serialize
             *
             * @param string $name
             */
            public function addColumnInputName($names)
            {
                if (is_array($names)) {
                    foreach ($names as $name) {
                        $this->addColumnInputName($name);
                    }
                } else {
                    if (!in_array($names, $this->_inputsToSerialize)) {
                        $this->_inputsToSerialize[] = $names;
                    }
                }
            }

            /**
             * Get grid column input names to serialize
             *
             * @return unknown
             */
            public function getColumnInputNames($asJSON = false)
            {
                if ($asJSON) {
                    if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                        return Mage::helper('core')->jsonEncode($this->_inputsToSerialize);
                    } else {
                        return Zend_Json::encode($this->_inputsToSerialize);
                    }
                }
                return $this->_inputsToSerialize;
            }

            /**
             * Get object data as JSON
             *
             * @return string
             */
            public function getDataAsJSON()
            {
                $result = array();
                if ($serializeData = $this->getSerializeData()) {
                    $result = $serializeData;
                } elseif (!empty($this->_inputsToSerialize)) {
                    return '{}';
                }
                if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                    return Mage::helper('core')->jsonEncode($result);
                } else {
                    return Zend_Json::encode($result);
                }
            }


            /**
             * Initialize grid block
             *
             * Get grid block from layout by specified block name
             * Get serialize data to manage it (called specified method, that return data to manage)
             * Also use reload param name for saving grid checked boxes states
             *
             *
             * @param Mage_Adminhtml_Block_Widget_Grid | string $grid grid object or grid block name
             * @param string $callback block method  to retrieve data to serialize
             * @param string $hiddenInputName hidden input name where serialized data will be store
             * @param string $reloadParamName name of request parametr that will be used to save setted data while reload grid
             */
            public function initSerializerBlock($grid, $callback, $hiddenInputName, $reloadParamName = 'entityCollection')
            {
                if (is_string($grid)) {
                    $grid = $this->getLayout()->getBlock($grid);
                }
                if ($grid instanceof Mage_Adminhtml_Block_Widget_Grid) {
                    $this->setGridBlock($grid)
                        ->setInputElementName($hiddenInputName)
                        ->setReloadParamName($reloadParamName)
                        ->setSerializeData($grid->$callback());
                }
            }

        }
    }
}