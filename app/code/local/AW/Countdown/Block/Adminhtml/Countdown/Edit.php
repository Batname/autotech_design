<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Countdown
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */




class AW_Countdown_Block_Adminhtml_Countdown_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {

        parent::__construct();
        $this->_objectId = 'countdownid';
        $this->_blockGroup = 'awcountdown';
        $this->_controller = 'adminhtml_countdown';

        $this->_updateButton('save', 'label', Mage::helper('awcountdown')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('awcountdown')->__('Delete Item'));
        $this->_updateButton('reset', 'label', Mage::helper('awcountdown')->__('Reset'));
        $this->_updateButton('back', 'label', Mage::helper('awcountdown')->__('Back'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit(\'' . $this->_getSaveAndContinueUrl() . '\')',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
                function saveAndContinueEdit(url) {
                //alert(url.replace(/{{tab_id}}/ig,countdown_tabsJsTabs.activeTab.id));
                                  editForm.submit(url.replace(/{{tab_id}}/ig,countdown_tabsJsTabs.activeTab.id));
                }
            function insertText(textBox, strNewText){
              var tb = textBox;
              var first = tb.value.slice(0, tb.selectionStart);
              var second = tb.value.slice(tb.selectionStart);
              tb.value = first + strNewText + second;
            }
        ";
        if ($this->getRequest()->getParam('id')) {
            $this->_addButton('saveasnew', array(
                'label' => Mage::helper('adminhtml')->__('Save As New'),
                'onclick' => 'saveAsNew()',
                'class' => 'scalable add',
                    ), -100);

            $this->_formScripts[] = "";
        }
    }

    protected function _getSaveAndContinueUrl() {
        return $this->getUrl('*/*/save', array(
                    '_current' => true,
                    'back' => 'edit',
                    'tab' => '{{tab_id}}'
                ));
    }

    public function getHeaderText() {
        return $this->__('Countdown Timer');
    }

}