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

class AW_Clickcheckout_OneclickController extends Mage_Core_Controller_Front_Action
{

    /**
     * Checks is quote virtual or has items
     * if empty or virtual it will return true
     * @return bool
     */
    private function _checkQuote()
    {
        $quote = Mage::getModel('checkout/cart')->getQuote();
        if ($quote->getData('items_count')) {
            $quote = $quote->getIsVirtual();
        } else {
            $quote = true;
        }
        return $quote;
    }

    /**
     * @return mixed
     */
    public function getOnepage()
    {
        return Mage::getSingleton('awclickcheckout/oneclick')->getOnepage();
    }

    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    /**
     * Load product by id in request
     * @return bool|Mage_Catalog_Product
     */
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    /*
     * Sets return url after login
     */
    public function loginAction(){
        $request = $this->getRequest();
        if($request->getParam('pid')){
        $product = Mage::getModel('catalog/product')->load($request->getParam('pid'));
        $this->_getCustomerSession()->setAfterAuthUrl($product->getProductUrl());
        }else{
            if($request->getParam('cart')){
                $this->_getCustomerSession()->setAfterAuthUrl(Mage::getUrl('checkout/cart'));
            }
        }
        $this->_redirectUrl(Mage::getUrl('customer/account/login'));
    }

    /**
     * Render popup
     */
    public function popupAction()
    {
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        $request = $this->getRequest();
        $_helper = Mage::helper('awclickcheckout');
        $response = Mage::getModel('awclickcheckout/oneclick');
        if ($request->getParam('billing_address_id') != '0') {
            $addresses = Mage::getModel('customer/session')->getCustomer()->getAddresses();
            foreach ($addresses as $address) {
                if ($address->getId() === $request->getParam('billing_address_id')) {
                    $customerAddress = Mage::getModel('customer/address')->load($address->getId());
                    $checkout = $this->getOnepage();
                    $checkout->initCheckout();
                    $checkout->saveCheckoutMethod('customer');
                    $checkout->saveBilling($customerAddress->getData(), $address->getId());
                }
            }
            if (!$this->_checkQuote()) {
                if ($request->getParam('shipping_address_id') != '0') {
                    foreach ($addresses as $address) {
                        if ($address->getId() === $request->getParam('shipping_address_id')) {
                            $customerAddress = Mage::getModel('customer/address')->load($address->getId());
                            $checkout->saveShipping($customerAddress->getData(), $address->getId());
                        }
                    }
                }
            }
        }
        $this->getOnepage()->saveCheckoutMethod('customer');
        $response->setShipping($_helper->renderShipping());
        $response->setPaymentm($_helper->renderPaymentMethods());
        $response->setSubtotals($_helper->renderTotals());
        $response->setAgreements($_helper->renderAgreements());
        $response->setPoints($_helper->renderPoints());
        if(Mage::helper('awall/versions')->getPlatform() != AW_All_Helper_Versions::CE_PLATFORM){
            $response->setPointsblock($_helper->renderPointsEE());
        }
        $response->send();
    }

    /**
     * Add product to cart
     * @return mixed
     */
    public function addToCartAction()
    {
        $cart   = $this->_getCart();
        $request = $this->getRequest();
        $params = $request->getParams();
        $billto = false;
        $shipto = false;
        if($request->getParam('billing_address_id')!='0'){
            $addresses = Mage::getModel('customer/session')->getCustomer()->getAddresses();
                foreach($addresses as $address){
                    if($address->getId()===$request->getParam('billing_address_id'))
                    {
                        $customerAddress = Mage::getModel('customer/address')->load($address->getId());
                        $checkout = $this->getOnepage();
                        $checkout->initCheckout();
                        $checkout->saveCheckoutMethod('customer');
                        $checkout->saveBilling($customerAddress->getData(), $address->getId());
                        $billto = true;
                    }
                }
            if($request->getParam('shipping_address_id')!='0'){
                foreach($addresses as $address){
                    if($address->getId()===$request->getParam('shipping_address_id'))
                    {
                        $customerAddress = Mage::getModel('customer/address')->load($address->getId());
                        $checkout->saveShipping($customerAddress->getData(), $address->getId());
                        $shipto = true;
                    }
                }
            }
        }
        if(!$billto){
            $redirect = Mage::getUrl('checkout/onepage');
        }
        if(!$shipto){
            $redirect = Mage::getUrl('checkout/onepage/index').'billing'.DS.$request->getParam('billing_address_id');
        }
        if($shipto && $billto){
            $this->_forward('add','cart','checkout',$params);
            return;
        }

        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_redirectUrl($redirect);
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(false);

        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
        }
        if(!Mage::helper('awclickcheckout')->canOnePage()){
            $redirect=Mage::getUrl('customer/address');
        }
        $this->_redirectUrl($redirect);
    }

    /**
     * Update subtotals and send it to html
     */
    private function _recalcTotals()
    {
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        $_helper = Mage::helper('awclickcheckout');
        $response = Mage::getModel('awclickcheckout/oneclick');
        $response->setSubtotals($_helper->renderTotals());
        $response->setPoints($_helper->renderPoints());
        $response->send();
    }

    /**
     * @param $shippingMethod
     */
    private function _shippingSave($shippingMethod)
    {
        $onepage = Mage::getModel('awclickcheckout/oneclick')->getOnepage();
        try {
            $onepage->saveShippingMethod($shippingMethod);
        } catch (Exception $e) {
        }
    }

    /**
     * @param $paymentMethod
     */
    private function _paymentSave($paymentMethod)
    {
        $onepage = Mage::getModel('awclickcheckout/oneclick')->getOnepage();
        try {
            $onepage->savePayment($paymentMethod);
        } catch (Exception $e) {
        }
    }

    /**
     * 1. Save shipping/payment methods.
     * 2. Recalculate totals
     */
    public function validateAction()
    {
        $request = $this->getRequest();
        if ($request->getParam('shipping_method')) {
            $this->_shippingSave($request->getParam('shipping_method'));
        }
        if ($request->getParam('payment', array())) {
            $this->_paymentSave($request->getParam('payment', array()));
        }
        $this->_recalcTotals();
    }

    /**
     * Render agreements
     */
    public function agreementsAction()
    {
        $layout = Mage::getSingleton('core/layout');
        $totals = $layout
            ->createBlock('awclickcheckout/popup_agreements')
            ->setTemplate('aw_clickcheckout/agreements.phtml');
        echo $totals->renderView();
    }
}