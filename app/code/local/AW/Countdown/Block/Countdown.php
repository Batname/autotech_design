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


class AW_Countdown_Block_Countdown extends Mage_Core_Block_Template {

    protected $_currentProduct = null;
    const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';
    public $_appliedCountdown = null;

    /* protected function _construct() {
      if(Mage::registry('current_product')!=null){
      $this->_appliedCountdown = $this->validateProduct();
      }
      } */

    private function _canShow($countdown) {

        $customerGroupId = Mage::helper('awcountdown')->getCustomerGroupId();

        if (!is_array($countdown->getData('customer_group_ids'))) {
            $countdown->setData('customer_group_ids', (array) explode(',', $countdown->getData('customer_group_ids')));
        }

        if (!in_array((string) $customerGroupId, (array) $countdown->getData('customer_group_ids'), true)) {
            return false;
        }

        $storeId = Mage::app()->getStore()->getId();
        if ($countdown->getCountdownid() != null) {
            if ($countdown->getStatus() == AW_Countdown_Model_Countdown::STATUS_STARTED) {
                if ($countdown->getIsEnabled() == '1') {
                    $timerStores = explode(',', $countdown->getStoreIds());
                    if (in_array($storeId, $timerStores) || in_array('0', $timerStores)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getProduct() {
        $this->setData('product', Mage::registry('current_product'));
        if (!$this->getData('product')) {

            $product = Mage::getModel('catalog/product')->load($this->getProductId());

            if (!$product->getId()) {
                return new Varien_Object();
            }

            $this->setData('product', $product);
        }

        return $this->getData('product');
    }

    protected function _beforeToHtml() {
        if ($this->getCountdownid() != null) {
            $tmp = $this->getCountdownModel()->load($this->getCountdownid());
            if ($tmp->getAutomDisplay() == AW_Countdown_Model_Source_Automation::NO) {
                if (Mage::registry('current_product') != null) {
                    $tmp = $tmp->validateProductAttributesWidget(Mage::registry('current_product'), $tmp);
                }
                $this->_appliedCountdown = $tmp;
            }
        } else {
            if (Mage::registry('current_product') != null) {
                $this->_appliedCountdown = $this->validateProduct();
            }
        }

        if ($this->_appliedCountdown && $this->_appliedCountdown->getId()) {
            if ($this->_canShow($this->_appliedCountdown)) {
                $this->setTemplate('aw_countdown/blocks.phtml');
            }
        }
    }

    public function getCountdownModel() {

        return Mage::getModel('awcountdown/countdown');
    }

    public function validateProduct() {

        $product = $this->getProduct();

        if (!$product->getId()) {
            return $this;
        }

        $validationModel = $this->getCountdownModel();

        if ($applied = $validationModel->validateProductAttributes($product)) {
            return $applied;
        }
        return $this;
    }

    public function getFormat($block) {
        return $block->getShowFormat();
    }

    public function getDesign($block) {
        return $block->getDesign();
    }

    public function getTimeLeft($block) {
        $date = $block->getDateTo();
        $timeShift = Mage::app()->getLocale()->date()->get(Zend_Date::TIMEZONE_SECS);
        $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $now = date(self::MYSQL_DATETIME_FORMAT, time() + $timeShift);
        return strtotime($date) - strtotime($now);
    }

    public function getBlockHtml($countdown) {
        $template = $countdown->getTemplate();
        $title = $countdown->getBlockTitle();
        $layout = Mage::getSingleton('core/layout');
        $timerHTML = $this->getLayout()->createBlock('awcountdown/timer')->toHtml();
        $content = str_replace('{{title}}', $title, $template);
        $content = str_replace('{{timer}}', $timerHTML . '<div class="clearer"></div>', $content);
        return $content;
    }

}
