<?php
/**
 * LiqPay Payment Module
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category		Mage
 * @package			Mage_PBLiqPay
 * @version			1.0
 * @author			Ivan A. Zhivkov (http://twitter.com/iniplanet, http://electronov.com.ua)
 * @copyright		Copyright (c) 2010 Ivan A. Zhivkov
 * @license			http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * 
 * EXTENSION INFORMATION
 * 
 * Magento			Community Edition 1.3.2.4
 * LiqPay API		Click&Buy 1.2 (https://www.liqpay.com/?do=pages&p=cnb12)
 * Way of payment	Visa / MasterCard, or LiqPay
 * 
 * DONATIONS ^_^
 * 
 * If you will like, you can make a donation via LiqPay on my phone +380936991178
 * 
 */

class Mage_PBLiqPay_Model_LiqPay extends Mage_Payment_Model_Method_Abstract {

		protected $_isGateway               = false;
		protected $_canAuthorize            = false;
		protected $_canCapture              = true;
		protected $_canCapturePartial       = false;
		protected $_canRefund               = false;
		protected $_canRefundInvoicePartial = false;
		protected $_canVoid                 = true;
		protected $_canUseInternal          = false;
		protected $_canUseCheckout          = true;
		protected $_canUseForMultishipping  = false;

		protected $_code = 'pbliqpay_liqpay';
		protected $_formBlockType = 'pbliqpay/liqpay_form';
		protected $_allowCurrencyCode = array('EUR', 'UAH', 'USD', 'RUB');

		protected $_order;

		public function getCheckout() {
				return Mage::getSingleton('checkout/session');
		}

		public function getQuote() {
				return $this->getCheckout()->getQuote();
		}

		public function createFormBlock($name) {
				$block = $this->getLayout()->createBlock('pbliqpay/liqpay_form', $name)
						->setMethod('pbliqpay_liqpay')
						->setPayment($this->getPayment())
						->setTemplate('pbliqpay/liqpay/form.phtml');

				return $block;
		}

		/**
		 * 
		 * Validate the currency code is available to use for LiqPay or not
		 * 
		 */
		public function validate() {
				parent::validate();

				$currency_code = $this->getQuote()->getBaseCurrencyCode();

				// Would like to use current currency?
				// $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();

				if (!in_array($currency_code, $this->_allowCurrencyCode)) {
						Mage::throwException(Mage::helper('pbliqpay')->__('Selected currency (').$currency_code.Mage::helper('pbliqpay')->__(') is incompatible with LiqPay.'));
				}

				return $this;
		}

		public function getOrderPlaceRedirectUrl() {
				return Mage::getUrl('pbliqpay/liqpay/redirect', array('_secure' => true));
		}

		public function getLiqPayUrl() {
				$url = 'https://liqpay.com/?do=clickNbuy';

				return $url;
		}

		/**
		 * 
		 * Collect data for xml
		 * 
		 */
		public function getXmlData() {
                $url_model = Mage::getModel('core/url')->setUseSession(false);
                // $currency_code = $this->getQuote()->getBaseCurrencyCode();
                // $amount = $this->getQuote()->getBaseGrandTotal();

                // Would like to use current currency?
                $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
                if(!$currency_code)
                {
                    $currency_code = 'UAH';
                }
                if($currency_code ==  'RUB')
                {
                   $currency_code = 'RUR';
                }
				// $amount = $this->getQuote()->getGrandTotal();
				
				$order_id = $this->getCheckout()->getLastRealOrderId();
				$order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
				$amount   = trim(round($order->getGrandTotal(), 2));
				
				// var_dump($currency_code, $amount);

				$sArr = array(
						'version'			=> '1.2',
						'result_url'		=> $url_model->getUrl('pbliqpay/liqpay/returnSuccess', array('_secure' => true)),
						'server_url'		=> $url_model->getUrl('pbliqpay/liqpay/notification', array('_secure' => true)),
						'merchant_id'		=> Mage::getStoreConfig('payment/pbliqpay_liqpay/merchant_id'),
						'order_id'			=> $order_id,
						'amount'			=> sprintf('%.2f', $amount),
						'currency'			=> $currency_code,
        				'description'		=> 'Payment for order '.$this->getCheckout()->getLastRealOrderId(),
						'default_phone'		=> Mage::getStoreConfig('payment/pbliqpay_liqpay/liqpay_phone'),
						'pay_way'			=> Mage::getStoreConfig('payment/pbliqpay_liqpay/liqpay_method'),
				);

				$sReq = '';
				$rArr = array();
				
				// Replacing "&" char with "and"
				foreach ($sArr as $k=>$v) {
						$value = str_replace("&", "and", $v);
						$rArr[$k] = $value;
						$sReq .= '&'.$k.'='.$value;
				}

				if ($sReq) {
						$sReq = substr($sReq, 1);
				}

				return $rArr;
		}

		/**
		 * 
		 * Make xml
		 * 
		 */
		public function getXml() {
				$xml = '<request>';
				$xml_data = $this->getXmlData();
				if ($xml_data) {
						foreach($xml_data as $field=>$value) {
								$xml .= '<'.$field.'>'.$value.'</'.$field.'>';
						}
				}
				$xml .= '</request>';

				return $xml;
		}

		public function getEncodedXml() {
				$encoded_xml = base64_encode($this->getXml());

				return $encoded_xml;
		}

		public function getSign() {
				$liqpay_signature = Mage::getStoreConfig('payment/pbliqpay_liqpay/liqpay_signature');
				$sign = base64_encode(sha1($liqpay_signature.$this->getXml().$liqpay_signature, 1));

				return $sign;
		}

		/**
		 * 
		 * Validate data from LiqPay server and update the database
		 * 
		 */
		public function processNotification() {
		
				$incoming_signature = $_POST['signature'];
				$incoming_xml = base64_decode($_POST['operation_xml']);
				$liqpay_signature = Mage::getStoreConfig('payment/pbliqpay_liqpay/liqpay_signature');
				$generated_signature = base64_encode(sha1($liqpay_signature.$incoming_xml.$liqpay_signature, 1));

				$xml_data = simplexml_load_string($incoming_xml);
				$response['version'] = $xml_data->version;
				$response['action'] = $xml_data->action;					// result_url, server_url
				$response['merchant_id'] = $xml_data->merchant_id;
				$response['order_id'] = $xml_data->order_id;
				$response['amount'] = $xml_data->amount;
				$response['currency'] = $xml_data->currency;
				$response['description'] = $xml_data->description;
				$response['status'] = $xml_data->status;					// Transaction status
				$response['code'] = $xml_data->code;						// Transaction error code
				$response['transaction_id'] = $xml_data->transaction_id;	// LiqPay transaction Id
				$response['pay_way'] = $xml_data->pay_way;
				$response['sender_phone'] = $xml_data->sender_phone;

				// When verified need to convert order into invoice
				$id = $response['order_id'];
				$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($id);				
				
				if ($incoming_signature != $generated_signature) {
						// Security check failed
						$order->addStatusToHistory(
								$order->getStatus(),
								Mage::helper('pbliqpay')->__('Security check failed!')
						)->save();
				} else {
						if ($order->getId()) {

								if ($response['amount'] != round($order->getBaseGrandTotal(), 2)) {
								
								// Would like to use current currency?
								// $order_currency = (string)$response['currency'];
								// $base_currency_code = Mage::getSingleton('directory/currency')->load(Mage::app()->getStore()->getBaseCurrencyCode());
								// $converted_amount = round($base_currency_code->convert($order->getBaseGrandTotal(), $order_currency), 2);
								
								// if ($response['amount'] != $converted_amount) {
										// When grand total does not equal, need to have some logic to take care
										$order->addStatusToHistory(
												$order->getStatus(),
												Mage::helper('pbliqpay')->__('Order total amount does not match LiqPay gross total amount.')
										)->save();
								} else {
										// Get from config order status to be set
										$newOrderStatus = $this->getConfigData('order_status', $order->getStoreId());
										if (empty($newOrderStatus)) {
												$newOrderStatus = $order->getStatus();
										}

										// Send New order e-mail to customer
										$order->sendNewOrderEmail();
										$order->setEmailSent(true);
										$order->save();
										$order->addStatusToHistory(
												$order->getStatus(),
												Mage::helper('pbliqpay')->__('New order e-mail was sent to customer.')
										)->save();

										// If verified set transaction in sale mode
										// If transaction in sale mode, we need to create an invoice
										// Otherwise transaction in authorization mode
										if ($response['status'] == 'success') {
												if (!$order->canInvoice()) {
														// When order cannot create invoice, need to have some logic to take care
														$order->addStatusToHistory(
																$order->getStatus(),
																Mage::helper('pbliqpay')->__('Error during creation of invoice.', true),
																$notified = true
														);
												} else {
														// Need to save transaction id
														$order->getPayment()->setTransactionId($response['transaction_id']);
														// Need to convert from order into invoice
														$invoice = $order->prepareInvoice();
														$invoice->register()->pay();
														Mage::getModel('core/resource_transaction')
																->addObject($invoice)
																->addObject($invoice->getOrder())
																->save();
														$order->setState(
																Mage_Sales_Model_Order::STATE_COMPLETE, true,
																Mage::helper('pbliqpay')->__('Invoice #%s created.', $invoice->getIncrementId()),
																// Would like to send e-mail about invoice?
																// $invoice->sendEmail(),
																// $invoice->setEmailSent(true),
																// $invoice->save(),
																$notified = true
														);
												}
										} elseif ($response['status'] == 'wait_secure') {
												$order->setState(
														Mage_Sales_Model_Order::STATE_PROCESSING, $newOrderStatus,
														Mage::helper('pbliqpay')->__('Waiting for verification from the LiqPay side.'),
														$notified = true
												);
										} elseif ($response['status'] == 'failure') {
												$order->setState(
														Mage_Sales_Model_Order::STATE_CANCELED, $newOrderStatus,
														Mage::helper('pbliqpay')->__('LiqPay error.'),
														$notified = true
												);
										}

										$order->save();
								}	
						}
				}	
		}

		public function isInitializeNeeded() {
				return true;
		}

		public function initialize($paymentAction, $stateObject) {
				$state = Mage_Sales_Model_Order::STATE_NEW;
				$stateObject->setState($state);
				$stateObject->setStatus(Mage::getSingleton('sales/order_config')->getStateDefaultStatus($state));
				$stateObject->setIsNotified(false);
				return $this;
		}

}