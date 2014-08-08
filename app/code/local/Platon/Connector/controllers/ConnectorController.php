<?php

class Platon_Connector_ConnectorController extends Mage_Core_Controller_Front_Action {

    /**
     * When a customer chooses Platon on Checkout/Payment page
     *
     */
    public function redirectAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setPlatonQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('platon/redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * When a customer cancel payment from Platon.
     */
    public function cancelAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPlatonQuoteId(true));
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * When Platon returns customer
     */
    public function successAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPlatonQuoteId(true));
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }

    /**
     *  Process Platon callback
     */
    public function processAction() {
        if (!$this->getRequest()->isPost()) {
            die("ERROR: Empty POST");
        }
        $data = $this->getRequest()->getPost();
        $answer = Mage::getModel('platon/main')->processCallback($data);
        die($answer);
    }

}

?>