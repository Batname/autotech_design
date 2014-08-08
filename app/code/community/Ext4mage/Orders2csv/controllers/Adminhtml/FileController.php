<?php
/**
* Ext4mage Orders2csv Module
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to Henrik Kier <info@ext4mage.com> so we can send you a copy immediately.
*
* @category   Ext4mage
* @package    Ext4mage_Orders2csv
* @copyright  Copyright (c) 2012 Ext4mage (http://ext4mage.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @author     Henrik Kier <info@ext4mage.com>
* */
class Ext4mage_Orders2csv_Adminhtml_FileController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
		->_setActiveMenu('orders2csv/file')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Orders2csv File structure'), Mage::helper('adminhtml')->__('Orders2csv File structure'));

		return $this;
	}

	public function indexAction() {
		$this->_initAction()
		->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('orders2csv/file')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('orders2csv_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('orders2csv/file');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('File element content'), Mage::helper('adminhtml')->__('File element content'));
				
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('orders2csv/adminhtml_file_edit'))
			->_addLeft($this->getLayout()->createBlock('orders2csv/adminhtml_file_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orders2csv')->__('Element does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
				
			$model = Mage::getModel('orders2csv/file');
			$columnModel = Mage::getModel('orders2csv/column');
			$model->setData($data)
			->setId($this->getRequest()->getParam('id'));
				
			try {
				if ($this->getRequest()->getParam('saveas') == 1) {
					$model->setId(null);
				}
				$model->save();
				
				foreach($data['file']['column'] as $column){
					$columnModel->setData(null);
					if($column['column_id']>0){
						$columnModel->setData($column)->setId($column['column_id']);
						if($column['is_delete']==1){
							$columnModel->delete();
							continue;
						}
						if ($this->getRequest()->getParam('saveas') == 1) {
							$columnModel->setData($column)->setId(null);
						}
					}else{
						$columnModel->setData($column)->setId(null);
						if($column['is_delete']==1){
							continue;
						}
					}

					$columnModel->setFileId($model->getId());
					$columnModel->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orders2csv')->__('Element was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back') || $this->getRequest()->getParam('save_as')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orders2csv')->__('Unable to find element to save'));
		$this->_redirect('*/*/');
	}

	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('orders2csv/file');
					
				$model->setId($this->getRequest()->getParam('id'))
				->delete();

				$columns = Mage::getResourceModel('orders2csv/column');
				$columnsList = $columns->getColumnByFile($this->getRequest()->getParam('id'));

				foreach($columnsList as $column){
					$columnModel = Mage::getModel('orders2csv/column')->load($column['column_id']);
					$columnModel->delete();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orders2csv')->__('File was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$fileIds = $this->getRequest()->getParam('file');
		if(!is_array($fileIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orders2csv')->__('Please select file element(s)'));
		} else {
			try {
				foreach ($fileIds as $fileId) {
					$file = Mage::getModel('orders2csv/file')->load($fileId);
					$file->delete();
					$columns = Mage::getResourceModel('orders2csv/column');
					$columnsList = $columns->getColumnByFile($fileId);
						
					foreach($columnsList as $column){
						$columnModel = Mage::getModel('orders2csv/column')->load($column['column_id']);
						$columnModel->delete();
					}
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('orders2csv')->__('Total of %d file element(s) were successfully deleted', count($fileIds))
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massStatusAction()
	{
		$fileIds = $this->getRequest()->getParam('file');
		if(!is_array($fileIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orders2csv')->__('Please select file element(s)'));
		} else {
			try {
				foreach ($fileIds as $fileId) {
					$file = Mage::getSingleton('orders2csv/file')
					->load($fileId)
					->setIsActive($this->getRequest()->getParam('status'))
					->setIsMassupdate(true)
					->save();
				}
				$this->_getSession()->addSuccess(
				Mage::helper('orders2csv')->__('Total of %d file element(s) were successfully updated', count($fileIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
}