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

class AW_Clickcheckout_Model_Observer
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
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getProductUrl(Mage_Catalog_Model_Product $product)
    {
        $query = array();
        if ($product->getHasOptions()) {
            $query = array('options' => 'cart');
        }
        return $product->getUrlModel()->getUrl($product, array('_query' => $query, '_secure' => Mage::app()->getRequest()->isSecure()));
    }

    /**
     * @return mixed
     */
    public function _getOnepage()
    {
        return Mage::getModel('awclickcheckout/oneclick')->getOnepage();
    }

    /**
     * @param $customerAddressId
     */
    private function saveBilling($customerAddressId)
    {
        $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
        try{
        $this->_getOnepage()->saveBilling($customerAddress->getData(), $customerAddressId);
        }catch(Exception $e){}
    }

    /**
     * @param $customerAddressId
     */
    private function saveShipping($customerAddressId)
    {
        $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
        try{
        $this->_getOnepage()->saveShipping($customerAddress->getData(), $customerAddressId);
        }catch(Exception $e){}
    }

    /**
     * @param AW_Clickcheckout_Model_Oneclick $response
     * @param $billing
     * @param null $shipping
     */
    private function _renderPopup(AW_Clickcheckout_Model_Oneclick $response, $billing, $shipping = null)
    {
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        $_helper = Mage::helper('awclickcheckout');
        $this->_getOnepage()->initCheckout();
        $this->_getOnepage()->saveCheckoutMethod('customer');
        $this->saveBilling($billing);
        if (!$this->_checkQuote()) {
            $this->saveShipping($shipping);
            $response->setShipping($_helper->renderShipping());
        }
        $response->setItems($_helper->renderItems());
        $response->setPaymentm($_helper->renderPaymentMethods());
        $response->setSubtotals($_helper->renderTotals());
        $response->setAgreements($_helper->renderAgreements());
        $response->setPoints($_helper->renderPoints());
        if(Mage::helper('awall/versions')->getPlatform() != AW_All_Helper_Versions::CE_PLATFORM){
            $response->setPointsblock($_helper->renderPointsEE());
        }
        if($_helper->acpInstalledAndEnabled()){
            //Add ACP JSON Data to response
            $acp_helper = Mage::helper('ajaxcartpro');
            $response->setCart($acp_helper->renderCart());
            $response->setLinks($acp_helper->renderTopCartLinkTitle());
        }
    }

    /**
     * @param $observer
     * @return mixed
     */
    public function addToCartEvent($observer)
    {

            $request = Mage::app()->getFrontController()->getRequest();
            $billing = $request->getParam('billing_address_id');
            $shipping = null;

            $shipping = $request->getParam('shipping_address_id');
            if ($request->getParam('oneclick')) {
                Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

                $_response = Mage::getModel('awclickcheckout/oneclick');
                $_product = $observer->getData('product');
                $_quote = Mage::getSingleton('checkout/session')->getQuote();
                $_helper = Mage::helper('awclickcheckout');
                $_quoteItem = null;
                $_qtyPassed = true;
                foreach ($_quote->getItemsCollection() as $_qa)
                    if ($_qa->getProduct()->getId() == $_product->getId())
                        $_quoteItem = $_qa;
                if ($_quoteItem) {
                    $stock = $_product->getStockItem()->getMaxSaleQty();
                    $_qtyPassed = $_product->getStockItem()->checkQty($_quoteItem->getQty()) && $stock > $_quoteItem->getQty();
                }
                if (!$_qtyPassed && !$_product->isGrouped() && !$_product->isConfigurable() && !$_helper->isProductBundle($_product)) {
                    $_response->setError($_helper->__('Wrong Qty'));
                    $_response->send();
                    return;
                }
                if ($billing) {
                    $_quote = Mage::getSingleton('checkout/session')->getQuote();
                    if ($shipping || $_quote->getIsVirtual()) {
                        $this->_renderPopup($_response, $billing, $shipping);
                    } else {
                        $_response->setRedirector('shipping');
                    }
                } else {
                    $_response->setRedirector('billing');
                }
                $_response->send();
            }

    }

    /**
     * @param $observer
     * @return mixed
     */
    public function provideIE9Compatibility($observer)
    {
        $body = $observer->getResponse()->getBody();
        if (strpos(strToLower($body), 'x-ua-compatible') !== false) {
            return;
        }
        $body = preg_replace('{(</title>)}i', '$1' . '<meta http-equiv="X-UA-Compatible" content="IE=8" />', $body);
        $observer->getResponse()->setBody($body);
    }


    /**
     * @param $observer
     */
    public function predispatchCheckoutCartAdd($observer)
    {

            $controllerAction = $observer->getControllerAction();
            $request = $controllerAction->getRequest();
            $params = $request->getParams();
            if ($request->getParam('oneclick')) {
                if (($pId = $request->getParam('product'))) {
                    $product = Mage::getModel('catalog/product')->load($pId);
                    $_otherPostCount = false;
                    foreach ($request->getPost() as $postOption => $postValue) {
                        if (!in_array($postOption, array(
                            'product',
                            'qty',
                            'related_product'
                        ))
                        ) {
                            $_otherPostCount = true;
                            break;
                        }
                    }
                    if ($_otherPostCount === false) {
                        $controllerAction->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                        $productUrl = $this->_getProductUrl($product);
                        $_response = Mage::getModel('awclickcheckout/oneclick');
                        $_response->setError(true)
                            ->setRedirect($productUrl)
                            ->send();
                    }
                }
            }
    }

}
