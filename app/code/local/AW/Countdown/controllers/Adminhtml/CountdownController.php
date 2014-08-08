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
 * @package    AW_Countdown
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Countdown_Adminhtml_CountdownController extends Mage_Adminhtml_Controller_Action {
    const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';
    protected $_countdownId = null;

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('promo/awcountdown')
                ->_addBreadcrumb(Mage::helper('awcountdown')->__('Countdown Timers'), Mage::helper('adminhtml')->__('Countdown Timers'));
        return $this;
    }

    public function indexAction() {
        return $this->_redirect('*/*/list');
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function listAction() {
        $this->_initAction()
                ->_setTitle($this->__('Blocks List'))
                ->_addContent(
                        $this->getLayout()->createBlock('awcountdown/adminhtml_countdown')
                )
                ->renderLayout();
    }

    public function massDeleteAction() {
        $countdownIds = $this->getRequest()->getParam('countdownid');
        if (!is_array($countdownIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($countdownIds as $countdownId) {
                    Mage::getModel('awcountdown/countdown')->load($countdownId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($countdownIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function massStatusAction() {
        $countdownIds = $this->getRequest()->getParam('countdownid');
        if (!is_array($countdownIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($countdownIds as $countdownId) {
                    $model = Mage::getModel('awcountdown/countdown')->load($countdownId);
                    $model->setData('is_enabled', $this->getRequest()->getParam('status'));
                    $model->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully updated', count($countdownIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('countdownid');
        $model = Mage::getModel('awcountdown/countdown');
        $triggerModel = Mage::getModel('awtrigger/trigger');
        $act = 'New Timer';
        if ($id) {
            $act = 'Edit Timer';
            $model->load($id);
            if (!$model->getCountdownid()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awcountdown')->__('This timer no longer exists'));
                return $this->_redirect('*/*');
            }
        }
        $this->_setTitle($this->__($act));
        $model->getConditions()->setJsFormObject('auto_conditions_fieldset');

        Mage::register('countdown_data', $model);
        Mage::register('triggers_data', $triggerModel);
        $this
                ->loadLayout();
        $this->_setActiveMenu('promo');

        $block = $this->getLayout()->createBlock('awcountdown/adminhtml_countdown_edit')
                ->setData('action', $this->getUrl('*/save'));

        $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true)
                ->setCanLoadRulesJs(true);

        $this
                ->_addContent($block)
                ->_addLeft($this->getLayout()->createBlock('awcountdown/adminhtml_countdown_edit_tabs'))
                ->renderLayout();
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('countdownid')) {
            try {
                Mage::getModel('awcountdown/countdown')->load($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awcountdown')->__('Timer was successfully deleted'));
                return $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return $this->_redirect('*/*/');
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awcountdown')->__('Delete error'));
        return $this->_redirect('*/*/');
    }

    public function saveAction() {

        if ($data = $this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('awcountdown/countdown');

                $data['conditions'] = $data['rule']['conditions'];
                if (isset($data['trigger'])) {
                    $triggers = $data['trigger'];
                    unset($data['trigger']);
                }
                unset($data['rule']);
                if ($data['store_ids']) {
                    if (is_array($data['store_ids'])) {
                        if (in_array('0', $data['store_ids'])) {
                            $data['store_ids'] = '0';
                        }else
                            $data['store_ids'] = implode(',', $data['store_ids']);
                    }
                }
                else {
                    $data['store_ids'] = '0';
                }

                $data = $this->_filterDateTime($data, array('date_from', 'date_to'));

                $now = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                if ($data['date_from'] == '') {
                    $data['date_from'] = $now;
                }

                //Check date

                if (strtotime($data['date_to']) > strtotime($data['date_from'])) {
                    $data['status'] = AW_Countdown_Model_Countdown::STATUS_STARTED;
                } else {
                    Mage::getSingleton('adminhtml/session')->addError('Ending date must be in future');
                    Mage::getSingleton('adminhtml/session')->setPageData($data);
                    $this->_redirect('*/*/edit', array('countdownid' => $this->getRequest()->getParam('countdownid')));
                    return;
                }

                if (strtotime($data['date_from']) > strtotime($now)) {
                    $data['status'] = AW_Countdown_Model_Countdown::STATUS_PENDING;
                }

                if (strtotime($data['date_to']) < strtotime($data['date_from'])) {
                    Mage::getSingleton('adminhtml/session')->addError('Ending date must be greater whan start date');
                    Mage::getSingleton('adminhtml/session')->setPageData($data);
                    $this->_redirect('*/*/edit', array('countdownid' => $this->getRequest()->getParam('countdownid')));
                    return;
                }

                if (!$this->getRequest()->getParam('countdownid')) {
                    $data['status'] = AW_Countdown_Model_Countdown::STATUS_PENDING;
                }

                if ($this->getRequest()->getParam('countdownid')) {
                    $model->load($this->getRequest()->getParam('countdownid'));
                    $model->clearMyTriggers();
                }

                $model->loadPost($data)->save();
                $this->_countdownId = $model->getCountdownid();
                //saving triggers
                if (isset($triggers)) {
                    $triggerModel = Mage::getModel('awtrigger/trigger');
                    foreach ($triggers as $trigger) {
                        $triggerModel->setData($trigger);
                        $triggerModel->setData('timer_id', $this->_countdownId);
                        $triggerModel->save();
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awcountdown')->__('Timer was successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', array('countdownid' => $model->getCountdownid(), 'tab' => Mage::app()->getRequest()->getParam('tab')));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('countdownid' => $this->getRequest()->getParam('countdownid')));
                return;
            }
        }

        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction() {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
                ->setId($id)
                ->setType($type)
                ->setRule(Mage::getModel('awcountdown/countdown'))
                ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Returns true when admin session contain error messages
     */
    protected function _hasErrors() {
        return (bool) count($this->_getSession()->getMessages()->getItemsByType('error'));
    }

    /**
     * Set title of page
     */
    protected function _setTitle($action) {
        if (method_exists($this, '_title')) {
            $this->_title($this->__('Countdown Timer'))->_title($this->__($action));
        }
        return $this;
    }

}