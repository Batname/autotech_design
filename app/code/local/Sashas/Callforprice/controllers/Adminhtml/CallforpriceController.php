<?php 
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Callforprice_Adminhtml_CallforpriceController extends Mage_Adminhtml_Controller_Action {
	
	public function _initAction()
	{
		$this->loadLayout()->_setActiveMenu('catalog');
	
		return $this;
	}
	
	protected function _construct()
	{
		// Define module dependent translate
		$this->setUsedModuleName('Sashas_Callforprice');
	}
	
	/**
	 * Check for is allowed
	 *
	 * @return boolean
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('sashas/callforprice');
	}	
	
	public function indexAction()
	{
		$this->loadLayout()->_setActiveMenu('catalog');
		$block = $this->getLayout()->createBlock('callforprice/adminhtml_uploadcontainer','callforprice_upload_form_container');
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		if(isset($_FILES['filecsv']['name']) and (file_exists($_FILES['filecsv']['tmp_name']))) {
			try {
				$uploader = new Varien_File_Uploader('filecsv');
				$uploader->setAllowedExtensions(array('csv'));
				$uploader->setAllowRenameFiles(true);
				$path = Mage::getBaseDir('var') . DS.'import'.DS;
				$filename=$uploader->getCorrectFileName($_FILES['filecsv']['name']);
				if (file_exists($path.$filename))
					unlink ($path.$filename);
				$uploader->save($path, $filename);
				$this->_getHelper()->processfile($path.$filename);
				Mage::getSingleton('core/session')->addSuccess("File was succefully processed.");
			}catch(Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError("File wasn't uploaded");
		}
		$this->_redirect('*/*/');
	}
	
	/**
	 * Retrieve base admihtml helper
	 *
	 * @return Mage_Adminhtml_Helper_Data
	 */
	protected function _getHelper()
	{
		return Mage::helper('callforprice');
	}
		
	
}

?>