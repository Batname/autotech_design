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


class AW_Countdown_Block_Adminhtml_Countdown_Edit_Tab_Triggers_Triggers extends Mage_Adminhtml_Block_Template {

    public function __construct() {
        $this->setTemplate('aw_countdown/triggers.phtml');
    }

    /**
     * @return string
     */
    protected function _toHtml() {
        $collection = Mage::getModel('awcountdown/trigger')
                ->getResourceCollection()
                ->addTimerIdFilter(Mage::registry('countdown_data')->getCountdownid())
                ->load();
        if ($collection->getSize() != 0) {
            $this->assign('triggers', $collection);
        } else {
            $this->assign('triggers', false);
        }
        return parent::_toHtml();
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout() {
        $this->setChild('deleteButton', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('awcountdown')->__('Remove'),
                            'onclick' => 'trigger.del(this)',
                            'class' => 'delete',
                            'style' => 'float:right;'
                        ))
        );

        $this->setChild('addButton', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('awcountdown')->__('Add Trigger'),
                            'onclick' => 'trigger.add(this)',
                            'class' => 'add'
                        ))
        );
        return parent::_prepareLayout();
    }

    /**
     * @return bool|object
     */
    public function getSaleRules() {
        $salesRules = Mage::getModel('salesrule/rule')->getCollection();
        if ($salesRules->getSize() == 0) {
            return false;
        }
        ;
        return $salesRules;
    }

    /**
     * @return bool|object
     */
    public function getCatalogRules() {
        $catalogRules = Mage::getModel('catalogrule/rule')->getCollection();
        if ($catalogRules->getSize() == 0) {
            return false;
        }
        ;
        return $catalogRules;
    }

    /**
     * @return string
     */
    public function getDeleteButtonHtml() {
        return $this->getChildHtml('deleteButton');
    }

    /**
     * @return string
     */
    public function getAddButtonHtml() {
        return $this->getChildHtml('addButton');
    }

    /**
     * @param $triggerId
     * @return mixed
     */
    public function getRuleName($triggerId) {
        $trigger = Mage::getModel('awtrigger/trigger')->load($triggerId);
        //get Sales Rule Name
        if ($trigger->getRuleType() === '0') {
            return Mage::getModel('salesrule/rule')->load($trigger->getRuleId())->getName();
        } else {
            return Mage::getModel('catalogrule/rule')->load($trigger->getRuleId())->getName();
        }
    }

}
