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


class AW_Countdown_Block_Design extends Mage_Core_Block_Template {

    protected function _prepareLayout() {

        $customerGroupId = Mage::helper('awcountdown')->getCustomerGroupId();
        $countdownCollection = Mage::getModel('awcountdown/countdown')->getCollection()
                ->addStoreIdsFilter(Mage::app()->getStore()->getId())
                ->addIsEnabledFilter(AW_Countdown_Model_Source_Status::ENABLED)
                ->addStatusFilter(AW_Countdown_Model_Countdown::STATUS_STARTED)
                ->addFieldToFilter('customer_group_ids', array("finset" => $customerGroupId))
                ->addActualDateFilter();

        $designs = array();

        foreach ($countdownCollection as $countdown) {

            $designs[] = $countdown->getData('design');
        }
        $designs = array_unique($designs);

        
        $head = $this->getLayout()->getBlock('head');
        foreach ($designs as $design) {
            $head->addCss('aw_countdown/' . $design . '/timer.css');
        }
    }

}
