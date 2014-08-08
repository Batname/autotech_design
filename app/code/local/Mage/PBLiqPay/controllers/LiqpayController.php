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

class Mage_PBLiqPay_LiqPayController extends Mage_Core_Controller_Front_Action {

		protected $_order;

		public function getSession() {
        		return Mage::getSingleton('checkout/session');
		}

		public function getPBLiqPay() {
				return Mage::getSingleton('pbliqpay/liqpay');
		}

		public function getOrder() {
				if ($this->_order == null) {
						$session = $this->getSession();
						$this->_order = Mage::getModel('sales/order');
						$this->_order->loadByIncrementId($session->getLastRealOrderId());
				}

				return $this->_order;
		}
 
		/**
		 * 
		 * Redirect customer to LiqPay payment interface
		 * 
		 */
		public function redirectAction() {
				$session = $this->getSession();

				$quote_id = $session->getQuoteId();
        		$last_real_order_id = $session->getLastRealOrderId();
				
				if (is_null($quote_id) || is_null($last_real_order_id)){
						$this->_redirect('checkout/cart/');
				} else {
						$session->setLiqPayQuoteId($quote_id);
						$session->setLiqPayLastRealOrderId($last_real_order_id);

						$order = $this->getOrder();
						$order->loadByIncrementId($last_real_order_id);

						$this->getResponse()->setHeader('Content-type', 'text/html; charset=windows-1251')->setBody($this->getLayout()->createBlock('pbliqpay/liqpay_redirect')->toHtml());

						// Add a message for Shop Admin about redirect action
						$order->addStatusToHistory(
								$order->getStatus(),
								Mage::helper('pbliqpay')->__('Customer switch over to LiqPay payment interface.')
						)->save();
						
						$session->getQuote()->setIsActive(false)->save();
						
						// Clear Shopping Cart
						$session->setQuoteId(null);
            			$session->setLastRealOrderId(null);
				}
		}

		/**
		 * 
		 * Customer successfully got back from LiqPay payment interface
		 * 
		 */
		public function returnSuccessAction() {
				$session = $this->getSession();

				$order_id = $session->getLiqPayLastRealOrderId();
				$quote_id = $session->getLiqPayQuoteId(true);

				$order = $this->getOrder();
				$order->loadByIncrementId($order_id);

				if ($order->isEmpty()) {
						return false;
				}

				// Add a message for Shop Admin about customer returning
				$order->addStatusToHistory(
						$order->getStatus(),
						Mage::helper('pbliqpay')->__('Customer successfully got back from LiqPay payment interface.')
				)->save();

				$session->setQuoteId($quote_id);
				$session->getQuote()->setIsActive(false)->save();
				$session->setLastRealOrderId($order_id);

				$this->_redirect('checkout/onepage/success', array('_secure' => true));
		}
		
		/**
		 * 
		 * Validate data from LiqPay server and update the database
		 * 
		 */
		public function notificationAction() {
				if (!$this->getRequest()->isPost()) {
						$this->norouteAction();
						return;
				}

				$this->getPBLiqPay()->processNotification($this->getRequest()->getPost());
		}

}
