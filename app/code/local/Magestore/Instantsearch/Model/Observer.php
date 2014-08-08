<?php

class Magestore_Instantsearch_Model_Observer {

	public function controller_action_predispatch_adminhtml($observer)
	{
		$controller = $observer->getControllerAction();
		if($controller->getRequest()->getControllerName() != 'system_config'
			|| $controller->getRequest()->getActionName() != 'edit')
			return;
		$section = $controller->getRequest()->getParam('section');
		if($section != 'instantsearch')
			return;
		$magenotificationHelper = Mage::helper('magenotification');
		if(!$magenotificationHelper->checkLicenseKey('Instantsearch')){
			$message = $magenotificationHelper->getInvalidKeyNotice();
			echo $message;die();
		}elseif((int)$magenotificationHelper->getCookieLicenseType() == Magestore_Magenotification_Model_Keygen::TRIAL_VERSION){
			Mage::getSingleton('adminhtml/session')->addNotice($magenotificationHelper->__('You are using a trial version of Product Instant Search extension. It will be expired on %s.',
														 $magenotificationHelper->getCookieData('expired_time')
											));
		}
	}		
}