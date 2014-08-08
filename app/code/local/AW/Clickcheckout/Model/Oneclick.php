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

class AW_Clickcheckout_Model_Oneclick extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('awclickcheckout/oneclick');
    }

    /**
     * @return Mage_Checkout_Type_Onepage
     */
    public function getOnepage(){
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * @param $htmlData
     */
    public function setSubtotals($htmlData)
    {
        $this->setData('subtotals',$htmlData);
    }

    /**
     * @param $htmlData
     */
    public function setItems($htmlData){
        $this->setData('items',$htmlData);
    }

    /**
     * @param $htmlData
     */
    public function setShipping($htmlData){
        $this->setData('shipping',$htmlData);
    }

    /**
     * @param $htmlData
     */
    public function setPaymentm($htmlData){
        $this->setData('paymentm',$htmlData);
    }

    /**
     * @param $htmlData
     */
    public function setAgreements($htmlData){
        $this->setData('agreements',$htmlData);
    }

    /**
     * @param $url
     */
    public function setRedirector($url)
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if($url=='billing'){
            if(Mage::helper('awclickcheckout')->canOnePage()){
                 $url=Mage::getUrl('checkout/onepage');
            }else{
                $url=Mage::getUrl('customer/address');
            }
        }else{
            if(Mage::helper('awclickcheckout')->canOnePage()){
                 $url=Mage::getUrl('checkout/onepage/index').'billing'.DS.$request->getParam('billing_address_id');
            }else{
                $url=Mage::getUrl('customer/address');
            }
            $addresses = Mage::getModel('customer/session')->getCustomer()->getAddresses();
            foreach($addresses as $address){
                if($address->getId()===$request->getParam('billing_address_id'))
                {
                    $customerAddress = Mage::getModel('customer/address')->load($address->getId());
                    $checkout = $this->getOnepage();
                    $checkout->initCheckout();
                    $checkout->saveCheckoutMethod('customer');
                    $checkout->saveBilling($customerAddress->getData(), $address->getId());
                    $this->setData('redirector',$url);
                }
            }
        }
        $this->setData('redirector',$url);
    }

    /**
     * Convert data to JSON and add it to response
     */
    public function send()
    {
        Mage::dispatchEvent('awclickcheckout_before_sending_response', array('response' => $this));
        Zend_Json::$useBuiltinEncoderDecoder = true;
        if ($this->getError()) $this->setR('error');
        else $this->setR('success');
        Mage::app()->getFrontController()->getResponse()->setBody(Zend_Json::encode($this->getData()));
    }
}
