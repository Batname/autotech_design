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

class Mage_PBLiqPay_Block_LiqPay_Redirect extends Mage_Core_Block_Abstract {

		protected function _toHtml() {
				$liqpay = Mage::getModel('pbliqpay/liqpay');

				$form = new Varien_Data_Form();
				$form->setAction($liqpay->getLiqPayUrl())
						->setId('pbliqpay_liqpay_checkout')
						->setName('pbliqpay_liqpay_checkout')
						->setMethod('POST')
						->setUseContainer(true);

				$form->addField('operation_xml', 'hidden', array(
						'name'			=> 'operation_xml',
						'value'			=> $liqpay->getEncodedXml()
				));
				$form->addField('signature', 'hidden', array(
						'name'			=> 'signature',
						'value'			=> $liqpay->getSign()
				));

				$html = '<html><body>';
				$html.= iconv('UTF-8', 'windows-1251', $this->__('You will be redirected to LiqPay payment interface in a few seconds.'));
				$html.= $form->toHtml();
				$html.= '<script type="text/javascript">document.getElementById("pbliqpay_liqpay_checkout").submit();</script>';
				$html.= '</body></html>';
		
				return $html;
		}

}