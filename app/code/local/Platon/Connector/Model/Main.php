<?php

/**
 * Our test CC module adapter
 */
class Platon_Connector_Model_Main extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'platon';
    protected $_formBlockType = 'platon/form';
    protected $_isGateway = false;
    protected $_canAuthorize = false;
    protected $_canCapture = false;
    protected $_canCapturePartial = false;
    protected $_canRefund = true;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
    protected $_canSaveCc = false;

    /*
     * @var string
     */

    const DEFAULT_LOG_FILE = 'platon_callback.log';

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject) {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

    /**
     * 
     * Draws form on Payment/Checkout page
     * 
     * @param type $name
     * @return object
     */
    public function createFormBlock($name) {
        $block = $this->getLayout()->createBlock('platon/form', $name)
                ->setMethod('platon')
                ->setPayment($this->getPayment())
                ->setTemplate('platon/form.phtml');

        return $block;
    }

    /**
     * 
     * Redirects to form action after saving order
     * 
     * @return type
     */
    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('platon/connector/redirect', array('_secure' => true));
    }

    /**
     * Returns form fields array for redirect form
     *
     * @return array
     */
    public function getFormFields() {
        $result = array();
        $result['key'] = $this->getConfigData('key');
        $orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $result['order'] = $orderIncrementId;
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $result['url'] = Mage::getUrl('platon/connector/success');
        $result['error_url'] = Mage::getUrl('platon/connector/cancel');

        /* Prepare product data for coding */
        $result['data'] = base64_encode(
                json_encode(
                        array(
                            'amount' => sprintf("%01.2f", $order->getGrandTotal()),
                            'name' => 'Order from ' . Mage::app()->getStore()->getGroup()->getName(),
                            'currency' => $order->getGlobalCurrencyCode()
                        )
                )
        );

        /* Calculation of signature */
        $sign = md5(
                strtoupper(
                        strrev($result['key']) .
                        strrev($result['data']) .
                        strrev($result['url']) .
                        strrev($this->getConfigData('pass'))
                )
        );

        $result['sign'] = $sign;

        return $result;
    }

    /**
     * 
     * Get payment gateway URL from config
     * 
     * @return string
     */
    public function getGatewayUrl() {
        return $this->getConfigData('gw_url');
    }

    /**
     * Process callback from Platon
     *  
     * @param array $data
     * @return string
     */
    public function processCallback($data) {
        $logObject = Mage::getModel('core/log_adapter', self::DEFAULT_LOG_FILE);
        // log callback data
        $logObject->log(var_export($data, 1));
        // generate signature from callback params
        $sign = md5(
                strtoupper(
                        strrev($data['email']) .
                        $this->getConfigData('pass') .
                        $data['order'] .
                        strrev(substr($data['card'], 0, 6) . substr($data['card'], -4))
                )
        );

        // verify signature
        if ($data['sign'] !== $sign) {
            // log failure
            $logObject->log("Invalid signature");
            return "ERROR: Bad signature";
        }
        // log success
        $logObject->log("Callback signature OK");
        $order = Mage::getModel('sales/order')->loadByIncrementId($data['order']);

        if (!$order->getId()) {
            // log wrong order
            $logObject->log("ERROR: Bad order ID");
            return "ERROR: Bad order ID";
        }

        // do processing stuff

        $payment = $order->getPayment();

        $payment
                ->setAmount($data['amount'])
                ->setTransactionId($data['id'])
                ->setPreparedMessage('');

        switch ($data['status']) {
            case 'SALE':
                $payment->setIsTransactionClosed(1)
                        ->registerCaptureNotification($data['amount'])
                        ->setStatus(self::STATUS_SUCCESS)
                        ->save();
                $order->save();

                // notify customer
                if ($invoice = $payment->getCreatedInvoice()) {
                    $comment = $order->sendNewOrderEmail()->addStatusHistoryComment(
                                    'Notified customer about invoice ' . $invoice->getIncrementId()
                            )
                            ->setIsCustomerNotified(true)
                            ->save();
                    $logObject->log("Invoice {$invoice->getIncrementId()} sent to customer");
                }
                $logObject->log("Order {$data['order']} processed as successfull sale");
                break;
            case 'REFUND':
                $this->refund($payment, $data['amount']);
                $order->setState('canceled')->setStatus('canceled')->save()->addStatusHistoryComment('Order refunded')->save();
                $logObject->log("Order {$data['order']} processed as successfull REFUND");
                break;
            case 'CHARGEBACK':
                $logObject->log("Order {$data['order']} processed as successfull chargeback");
                break;
            default:
                $logObject->log("Invalid callback data");
                return "ERROR: Invalid callback data";
        }


        return "OK";
    }

}

?>