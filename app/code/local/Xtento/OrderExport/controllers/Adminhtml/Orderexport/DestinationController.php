<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-12-21T17:13:32+01:00
 * File:          app/code/local/Xtento/OrderExport/controllers/Adminhtml/Orderexport/DestinationController.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Adminhtml_OrderExport_DestinationController extends Xtento_OrderExport_Controller_Abstract
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('xtento_orderexport/destination');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('This destination no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if ($model->getType() == Xtento_OrderExport_Model_Destination::TYPE_LOCAL) {
                if (!$model->getPath()) {
                    $model->setPath('./var/export/');
                }
            }
        } else {
            // Default values
        }

        $this->_title($model->getId() ? $model->getName() : Mage::helper('xtento_orderexport')->__('New Destination'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        } else {
            // Handle certain fields
        }

        Mage::register('destination', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('xtento_orderexport')->__('Edit Destination') : Mage::helper('xtento_orderexport')->__('New Destination'), $id ? Mage::helper('xtento_orderexport')->__('Edit Destination') : Mage::helper('xtento_orderexport')->__('New Destination'))
            ->_addContent($this->getLayout()->createBlock('xtento_orderexport/adminhtml_destination_edit')->setData('action', $this->getUrl('*/*/save')))
            ->_addLeft($this->getLayout()->createBlock('xtento_orderexport/adminhtml_destination_edit_tabs'));

        $this->renderLayout();

        if (Mage::getSingleton('adminhtml/session')->getDestinationDuplicated()) {
            Mage::getSingleton('adminhtml/session')->setDestinationDuplicated(0);
        }
    }

    private function _testConnection()
    {
        $destination = Mage::registry('destination');
        $testResult = Mage::getModel('xtento_orderexport/destination_'.$destination->getType(), array('destination' => $destination))->testConnection();
        if (!$testResult->getSuccess()) {
            Mage::getSingleton('adminhtml/session')->addWarning($testResult->getMessage());
        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess($testResult->getMessage());
        }
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getModel('xtento_orderexport/destination');
            $model->setData($postData);
            $model->setLastModification(now());

            // Handle certain fields
            if ($model->getId()) {
                $model->setPath(trim(rtrim($model->getPath(), '/')) . '/');
                if ($model->getNewPassword() !== '' && $model->getNewPassword() !== '******') {
                    $model->setPassword(Mage::helper('core')->encrypt($model->getNewPassword()));
                }
            }

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                Mage::register('destination', $model, true);
                if (isset($postData['destination_id']) && !$this->getRequest()->getParam('switch', false)) {
                    $this->_testConnection();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_orderexport')->__('The destination has been saved.'));
                if ($this->getRequest()->getParam('continue')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'active_tab' => $this->getRequest()->getParam('active_tab')));
                } else {
                    $this->_redirect('*/*');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                $message = $e->getMessage();
                if (preg_match('/Notice: Undefined offset: /', $e->getMessage()) && preg_match('/SSH2/', $e->getMessage())) {
                    $message = 'This doesn\'t seem to be a SFTP server.';
                }
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('An error occurred while saving this destination: '.$message));
            }

            Mage::getSingleton('adminhtml/session')->setFormData($postData);
            $this->_redirectReferer();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('Could not find any data to save in the POST request. POST request too long maybe?'));
            $this->_redirect('*/*');
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('xtento_orderexport/destination');
        $model->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('This destination does not exist anymore.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_orderexport')->__('Destination has been successfully deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('Please select a destination to duplicate.'));
            return $this->_redirect('*/*');
        }

        try {
            $model = Mage::getModel('xtento_orderexport/destination');
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('This destination does not exist anymore.'));
                return $this->_redirect('*/*');
            }

            $destination = clone $model;
            $destination->setEnabled(0);
            $destination->setId(null);
            $destination->setLastModification(now());
            $destination->save();

            Mage::getSingleton('adminhtml/session')->setDestinationDuplicated(1);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_orderexport')->__('The destination has been duplicated. Please make sure to enable it.'));
            $this->_redirect('*/*/edit', array('id' => $destination->getId()));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    public function massUpdateStatusAction()
    {
        $ids = $this->getRequest()->getParam('destination');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_orderexport')->__('Please select destinations to modify.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('xtento_orderexport/destination');
                $model->load($id);
                $model->setEnabled($this->getRequest()->getParam('enabled'));
                $model->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully updated', count($ids)));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/orderexport')
            ->_title(Mage::helper('xtento_orderexport')->__('Sales Export'))->_title(Mage::helper('xtento_orderexport')->__('Destinations'));
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/orderexport/destination');
    }
}