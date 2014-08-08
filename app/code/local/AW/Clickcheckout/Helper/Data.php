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
 * @package    AW_Clickcheckout
 * @version    1.1.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Clickcheckout_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @param $key
     * @return mixed|null|string
     */
    public function getAWOCCGeneralParam($key)
    {
        return Mage::getStoreConfig("awclickcheckout/general/{$key}");
    }

    /**
     * Checks is onepage checkout is enabled
     * @return mixed
     */
    public function canOnePage(){
      return Mage::helper('checkout')->canOnepageCheckout();
    }

    private function _refreshQuoteTotals(){
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $quote->setTotalsCollectedFlag(false);
        $new = $quote->collectTotals();
        $new->save();
    }
    /**
     * Checks is current product virtual and check is quote is virtual
     * It's needed to solve: is needed to display shipping address and shipping methods
     * @return bool
     */
    public function checkVirtual()
    {
        $quote = Mage::getModel('checkout/cart')->getQuote();
        if($quote->getData('items_count')){
        $quote = $quote->getIsVirtual();
        }else{
            $quote = true;
        }
        $product = Mage::registry('current_product');
        return $quote && $product->isVirtual();
    }

    /**
     * @return bool
     * check is extension is enabled output
     */
    public function isEnabled()
    {
        if ((bool)Mage::getStoreConfig('advanced/modules_disable_output/AW_Clickcheckout'))
            return false;
            return true;
    }

    /**
     * Render Totals html
     * @return mixed
     */
    public function renderTotals() {
        $this->_refreshQuoteTotals();
        $layout = Mage::getSingleton('core/layout');
        $totals = $layout
                ->createBlock('awclickcheckout/popup_totals')
                ->setTemplate('aw_clickcheckout/popup/totals.phtml');
        return $totals->renderView();
    }

    /**
     * Render Agreements html
     * @return mixed
     */
    public function renderAgreements()
    {
        $layout = Mage::getSingleton('core/layout');
        $agreements = $layout
                ->createBlock('awclickcheckout/popup_agreements')
                ->setTemplate('aw_clickcheckout/popup/agreements.phtml');
        return $agreements->renderView();
    }

    /**
     * Render Cart Items Table html
     * @return mixed
     */
    public function renderItems()
    {
        $layout = Mage::getSingleton('core/layout');
        $items=$layout->createBlock('awclickcheckout/popup_items')
            ->addItemRender('default', 'checkout/cart_item_renderer', 'checkout/onepage/review/item.phtml')
                           ->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/onepage/review/item.phtml')
                           ->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/onepage/review/item.phtml')
                           ->addItemRender('downloadable', 'downloadable/checkout_cart_item_renderer', 'downloadable/checkout/onepage/review/item.phtml')
                           ->addItemRender('bundle', 'bundle/checkout_cart_item_renderer', 'checkout/onepage/review/item.phtml')
                           ->addItemRender('subscription_simple', 'sarp/checkout_cart_item_renderer_simple', 'checkout/onepage/review/item.phtml')
                           ->addItemRender('subscription_configurable', 'sarp/checkout_cart_item_renderer_configurable', 'checkout/onepage/review/item.phtml')
                           ->addItemRender('subscription_downloadable', 'sarp/checkout_cart_item_renderer_downloadable', 'downloadable/checkout/onepage/review/item.phtml')
                           ->addItemRender('subscription_grouped', 'sarp/checkout_cart_item_renderer_grouped', 'checkout/onepage/review/item.phtml')
                           ->addItemRender('bookable', 'booking/checkout_cart_item_renderer', 'checkout/onepage/review/item.phtml')
                           ->addItemRender('giftcard', 'enterprise_giftcard/checkout_cart_item_renderer', 'checkout/onepage/review/item.phtml')
                           ->setTemplate('aw_clickcheckout/popup/items.phtml');
        return $items->renderView();
    }

    /**
     * Render Shipping methods html
     * @return mixed
     */
    public function renderShipping()
    {
        $layout = Mage::getSingleton('core/layout');
        $shipping = $layout
                ->createBlock('awclickcheckout/popup_shipping')
                ->setTemplate('aw_clickcheckout/popup/shipping.phtml');
        return $shipping->renderView();
    }

    /**
     * Check is AW_Points installed, enabled and compatibility is enabled
     * @return bool
     */
    public function pointsInstalledAndEnabled(){
        if($this->isExtensionInstalled('AW_Points')){
            if(Mage::helper('points/config')->isPointsEnabled()){
                if((bool)$this->getAWOCCGeneralParam('points')){
                    return true;
                }
            }
        }
        return false;
    }

    /*
     * Render points info block
     */
    public function renderPoints(){
        if($this->pointsInstalledAndEnabled()){
            $layout = Mage::getSingleton('core/layout');
            $points = $layout
                ->createBlock('awclickcheckout/popup_points')
                ->setTemplate('aw_clickcheckout/popup/points.phtml');
            return $points->renderView();
        }
        if(Mage::helper('awall/versions')->getPlatform() != AW_All_Helper_Versions::CE_PLATFORM){
            $layout = Mage::getSingleton('core/layout');
            $points = $layout
                    ->createBlock('awclickcheckout/popup_points_tooltip')
                    ->setTemplate('aw_clickcheckout/popup/tooltip.phtml');
                return $points->renderView();
        }

        return '';
    }

    /**
     *
     */
    public function renderPointsEE(){
        $layout = Mage::getSingleton('core/layout');
        $points = $layout
                        ->createBlock('awclickcheckout/popup_points_additional')
                        ->setTemplate('aw_clickcheckout/popup/additional.phtml');
        return $points->renderView();
    }
    /**
     * Render Payment methods html
     * @return mixed
     */
    public function renderPaymentMethods()
    {
        $layout = Mage::getSingleton('core/layout');
        if($this->pointsInstalledAndEnabled()){
                $payment = $layout
                        ->createBlock('awclickcheckout/popup_pointsm')
                        ->setTemplate('aw_clickcheckout/popup/pointsm.phtml');
        }else{
            $payment = $layout
                ->createBlock('awclickcheckout/popup_paymentm')
                ->setTemplate('aw_clickcheckout/popup/paymentm.phtml');
        }
        return $payment->renderView();
    }

    /**
     * Save shipping method in quote
     * @param $rateCode
     */
    public function setShipping($rateCode)
    {
        $onepage=Mage::getModel('awclickcheckout/oneclick')->getOnepage();
        try{
            $onepage->saveShippingMethod($rateCode);
        } catch(Exception $e){}
    }

    /**
     * @param $product
     * @return bool
     */
    public function isProductBundle($product) {
        return $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
    }

    /**
     * Check is AW AjaxCart Pro is installed and enabled
     * @return bool
     */
    public function acpInstalledAndEnabled(){
        if($this->isExtensionInstalled('AW_Ajaxcartpro')){
            return true;
        }
        return false;
    }

    //checks is extension $name installed
    public static function isExtensionInstalled($name)
    {
       $modules = (array) Mage::getConfig()->getNode('modules')->children();
       return array_key_exists($name, $modules)
               &&  'true' == (string) $modules[$name]->active
               && !(bool) Mage::getStoreConfig('advanced/modules_disable_output/'.$name);
    }
}
