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




class AW_Countdown_Block_Adminhtml_Countdown_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('countdown_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('awcountdown')->__('Countdown Timer'));
    }

    protected function _beforeToHtml() {

        $this->addTab('general', array(
            'label' => Mage::helper('awcountdown')->__('General'),
            'title' => Mage::helper('awcountdown')->__('General'),
            'content' => $this->getLayout()->createBlock('awcountdown/adminhtml_countdown_edit_tab_general')->toHtml(),
            'active' => ($this->getRequest()->getParam('tab') == 'countdown_tabs_general') ? true : false,
        ));
        $this->addTab('design', array(
            'label' => Mage::helper('awcountdown')->__('Design'),
            'title' => Mage::helper('awcountdown')->__('Design'),
            'content' => $this->getLayout()->createBlock('awcountdown/adminhtml_countdown_edit_tab_design')->toHtml(),
            'active' => ($this->getRequest()->getParam('tab') == 'countdown_tabs_design') ? true : false,
        ));

        $this->addTab('automation', array(
            'label' => Mage::helper('awcountdown')->__('Automation'),
            'title' => Mage::helper('awcountdown')->__('Automation'),
            'content' => $this->getLayout()->createBlock('awcountdown/adminhtml_countdown_edit_tab_automation')->toHtml(),
            'active' => ($this->getRequest()->getParam('tab') == 'countdown_tabs_automation') ? true : false,
        ));

        $this->addTab('triggers', array(
            'label' => Mage::helper('awcountdown')->__('Triggers'),
            'title' => Mage::helper('awcountdown')->__('Triggers'),
            'content' => $this->getLayout()->createBlock('awcountdown/adminhtml_countdown_edit_tab_triggers')
                    ->append($this->getLayout()->createBlock('awcountdown/adminhtml_countdown_edit_tab_triggers_triggers'))
                    ->toHtml(),
            'active' => ($this->getRequest()->getParam('tab') == 'countdown_tabs_triggers') ? true : false,
        ));

        return parent::_beforeToHtml();
    }

}