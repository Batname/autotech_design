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


/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Countdown
 * @copyright  Copyright (c) 2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 *
 * Events:
 * awcountdown_execute_onstart_triggers
 * awcountdown_execute_onend_triggers
 *
 */
class AW_Countdown_Model_Cron extends Mage_Core_Model_Abstract {
    const CACHE_ENABLED = false;
    const LOCK = 'awcountdowncronlock';
    const LOCKTIME = 300; // 5 minutes
    const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    public static function run() {

        $timeShift = Mage::app()->getLocale()->date()->get(Zend_Date::TIMEZONE_SECS);
        $now = date(self::MYSQL_DATETIME_FORMAT, time() + $timeShift);
        if (self::checkLock()) {
            $catalogRulesCounter = 0;
            //**************************
            //Get Timers for onStart event
            //**************************
            $countdownsOnStart = Mage::getModel('awcountdown/countdown')->getCollection()
                            ->addStatusFilter(AW_Countdown_Model_Countdown::STATUS_PENDING)
                            ->addDateFromFilter($now)
                            ->addIsEnabledFilter('1')->load();

            //process timers onstart triggers
            if ($countdownsOnStart->getSize() != 0) {
                foreach ($countdownsOnStart as $timer) {
                    $onstartTriggers = Mage::getModel('awtrigger/trigger')->getCollection()
                            ->addTimerIdFilter($timer->getCountdownid())
                            ->addActionTypeFilter(AW_Countdown_Model_Trigger::ON_START)
                            ->load();
                    if ($onstartTriggers->getSize() != 0) {
                        //Run triggers
                        foreach ($onstartTriggers as $trigger) {
                            try {
                                if ($trigger->getRuleType() == '0') {
                                    //ShoppingCart rules
                                    $rule = Mage::getModel('salesrule/rule')->load($trigger->getRuleId());
                                } else {
                                    //CatalogRule
                                    $rule = Mage::getModel('catalogrule/rule')->load($trigger->getRuleId());
                                    $catalogRulesCounter++;
                                }
                                if ($rule->getId()) {
                                    $rule->setData('is_active', $trigger->getAction())->save();
                                }
                            } catch (Exception $e) {
                                
                            }
                        }
                    }
                    Mage::dispatchEvent('awcountdown_execute_onstart_triggers', array('triggers' => $onstartTriggers, 'countdowns' => $countdownsOnStart));
                    Mage::helper('awcore/logger')->log($timer, 'Countdown #' . $timer->getCountdownid() . ' started.', null, 'Executed triggers count: ' . $onstartTriggers->getSize());
                    $timer->setData('status', AW_Countdown_Model_Countdown::STATUS_STARTED)->save();
                }
            }
            //**************************
            //Get Timers for onEnd event
            //**************************
            $countdownsOnEnd = Mage::getModel('awcountdown/countdown')->getCollection()
                            ->addStatusFilter(AW_Countdown_Model_Countdown::STATUS_STARTED)
                            ->addDateToFilter($now)
                            ->addIsEnabledFilter('1')->load();
            //process timers onstart triggers
            if ($countdownsOnEnd->getSize() != 0) {
                foreach ($countdownsOnEnd as $timer) {
                    $onendTriggers = Mage::getModel('awtrigger/trigger')->getCollection()
                            ->addTimerIdFilter($timer->getCountdownid())
                            ->addActionTypeFilter(AW_Countdown_Model_Trigger::ON_END)
                            ->load();
                    if ($onendTriggers->getSize() != 0) {
                        //Run triggers
                        foreach ($onendTriggers as $trigger) {
                            try {
                                if ($trigger->getRuleType() == '0') {
                                    //ShoppingCart rules
                                    $rule = Mage::getModel('salesrule/rule')->load($trigger->getRuleId());
                                } else {
                                    //CatalogRule
                                    $rule = Mage::getModel('catalogrule/rule')->load($trigger->getRuleId());
                                    $catalogRulesCounter++;
                                }
                                if ($rule->getId()) {
                                    $rule->setData('is_active', $trigger->getAction())->save();
                                }
                            } catch (Exception $e) {
                                
                            }
                        }
                    }
                    Mage::dispatchEvent('awcountdown_execute_onend_triggers', array('triggers' => $onendTriggers, 'countdowns' => $countdownsOnEnd));
                    Mage::helper('awcore/logger')->log($timer, 'Countdown #' . $timer->getCountdownid() . ' ended.', null, 'Executed triggers count: ' . $onendTriggers->getSize());
                    $timer->setData('status', AW_Countdown_Model_Countdown::STATUS_ENDED)->save();
                }
            }
            if ($catalogRulesCounter > 0) {
                try {
                    Mage::getModel('catalogrule/rule')->applyAll();
                    Mage::app()->removeCache('catalog_rules_dirty');
                } catch (Exception $e) {
                    
                }
            }
            Mage::app()->removeCache(self::LOCK);
        }
    }

    public static function checkLock() {
        if ($time = Mage::app()->loadCache(self::LOCK)) {
            if ((time() - $time) < self::LOCKTIME)
                return false;
        }
        Mage::app()->saveCache(time(), self::LOCK, array(), self::LOCKTIME);
        return true;
    }

}
