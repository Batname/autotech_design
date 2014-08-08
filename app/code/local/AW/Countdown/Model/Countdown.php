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


if (AW_Countdown_Helper_Data::isNewRules()) {

    abstract class AW_Countdown_Model_Countdown_Abstract extends Mage_Rule_Model_Abstract {

        public function getConditionsInstance() {
            return Mage::getModel('rule/condition_combine');
        }

        public function getActionsInstance() {
            return Mage::getModel('rule/action_collection');
        }
    }

} else {
    class AW_Countdown_Model_Countdown_Abstract extends Mage_Rule_Model_Rule { }
}

class AW_Countdown_Model_Countdown extends AW_Countdown_Model_Countdown_Abstract {
    //Countdown's statuses

    const STATUS_PENDING = '0';
    const STATUS_STARTED = '1';
    const STATUS_ENDED = '2';

    public function _construct() {
       
        $this->_init('awcountdown/countdown');
    }

    /**
     *
     */
    public function clearMyTriggers() {
        $collection = Mage::getModel('awcountdown/trigger')
                ->getResourceCollection()
                ->addTimerIdFilter($this->getCountdownid())
                ->load();
        foreach ($collection as $trigger) {
            $trigger->delete();
        }
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getConditionsInstance() {
        return Mage::getModel('awcountdown/countdown_condition_combine');
    }

    /**
     * @param Varien_Object $product
     * @return bool
     */
    public function validateProductAttributes(Varien_Object $product) {

        $customerGroupId = Mage::helper('awcountdown')->getCustomerGroupId();
        $countdownCollection = Mage::getModel('awcountdown/countdown')->getCollection()
                ->addStoreIdsFilter(Mage::app()->getStore()->getId())
                ->addAutomDisplayFilter(AW_Countdown_Model_Source_Automation::INSIDE_PRODUCT_PAGE)
                ->addIsEnabledFilter(AW_Countdown_Model_Source_Status::ENABLED)
                ->addStatusFilter(self::STATUS_STARTED)
                ->addFieldToFilter('customer_group_ids', array("finset" => $customerGroupId))
                ->addActualDateFilter()
                ->orderByDateTo(Zend_Db_Select::SQL_ASC);


        foreach ($countdownCollection as $countdown) {

            $countdown->load($countdown->getId());

            if ($countdown->validate($product)) {
                return $countdown;
            }
        }
        return false;
    }

    /**
     * @param Varien_Object $product
     * @param $countdown
     * @return bool
     */
    public function validateProductAttributesWidget(Varien_Object $product, $countdown) {
        if ($countdown->validate($product)) {
            return $countdown;
        }
        return false;
    }

    public function awSaveBefore() {

        return true;
    }

    protected function _beforeSave() {

        parent::_beforeSave();

        if (is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
        }
    }

}
