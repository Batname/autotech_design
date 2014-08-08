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
 * @package    AW_Zblocks
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Zblocks_Adminhtml_ZblocksController extends Mage_Adminhtml_Controller_Action
{
    /*
     * Iniitializes page layout
     */
    protected function _initAction() 
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/zblocks')
            ->_addBreadcrumb($this->__('Z-Blocks'), $this->__('Block Manager'));

        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    /*
     * Edit Z-Block action
     */
    public function editAction() 
    {
        $session = Mage::getSingleton('adminhtml/session');
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('zblocks/zblocks')->load($id);

        if($model->getId() || !$id)
        {
            $data = $model->getData();
            $sessionData = $session->getZBlocksData(true);
            if(is_array($sessionData)){
                unset($sessionData['store_ids']);
                $data = array_merge($data, $sessionData);
            }
            $session->setZBlocksData(false);

            Mage::register('zblocks_data', $data);

            $this->loadLayout();
            $this->_setActiveMenu('cms/zblocks');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            
            $this->_addContent($this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit'))
                ->_addLeft($this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tabs'));

            $this->renderLayout();
        } else {
            $session->addError($this->__('The block does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() 
    {
        $this->_forward('edit');
    }

    /*
     * Parses date and time
     * @return int Parsing errors count
     */
    private function _timeParseErrors($time)
    {
        $data = date_parse($time);
        return $data['error_count'];
    }

    /*
     * Save Z-Block Action
     */
    public function saveAction() 
    {
        $session = Mage::getSingleton('adminhtml/session');
        $session->setZBlocksData(false);

        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('zblocks/zblocks');
            $id = $this->getRequest()->getParam('id');

            if(!isset($data['category_ids'])
            &&  $id
            ) $oldData = $model->load($id)->getData(); // to get category_ids from the table
            else $oldData = array();

            $data = array_merge($oldData, $data);

            $model->setData($data)->setId($id);
            try
            {
                if($model->getBlockPosition()=='custom' && !$model->getBlockPositionCustom())
                {
                    $session->addError($this->__('Please set block Custom Position identifier'));
                    $session->setZBlocksData($data);
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }

                $categoryIds = array_unique(explode(',', $model->getCategoryIds()));
                foreach($categoryIds as $key => $value)
                    if(!$value) unset($categoryIds[$key]);
                $model->setCategoryIds(implode(',', $categoryIds));

                $model->setStoreIds(implode(',', $model->getStoreIds()));
                if ($model->getCreationTime == NULL) $model->setCreationTime(now());
                $model->setUpdateTime(now());

                // check if schedule date was entered correctly
                $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

                if($date = $model->getScheduleFromDate())
                {
                    $date = Mage::app()->getLocale()->date($date, $format, null, false);
                    $model->setScheduleFromDate($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                } 
                else $model->setScheduleFromDate(null);

                if($date = $model->getScheduleToDate()) 
                {
                    $date = Mage::app()->getLocale()->date($date, $format, null, false);
                    $model->setScheduleToDate($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                } 
                else $model->setScheduleToDate(null);

                (!is_null($model->getScheduleFromDate()) && !is_null($model->getScheduleToDate()) && 
                    strtotime($model->getScheduleFromDate()) > strtotime($model->getScheduleToDate()))
                ? $dateError = $this->__('Start date can\'t be greater than end date')
                : $dateError = false;

                if(is_null($model->getScheduleFromDate())) $model->setScheduleFromDate(new Zend_Db_Expr('null'));
                if(is_null($model->getScheduleToDate())) $model->setScheduleToDate(new Zend_Db_Expr('null'));

                // check if schedule time was entered correctly
                if($data['schedule_from_time'] && $this->_timeParseErrors($data['schedule_from_time'])) 
                    $parseError = $this->__('Schedule From Time');
                elseif($data['schedule_to_time'] && $this->_timeParseErrors($data['schedule_to_time'])) 
                    $parseError = $this->__('Schedule To Time');
                else $parseError = false;

                if($parseError || $dateError)
                {
                    if($parseError) $session->addError($this->__('Error in "%s" field', $parseError));
                    if($dateError) $session->addError($dateError);
                    $session->setZBlocksData($data);
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'tab' => 'schedule'));
                    return;
                }

                $model->save();

                $session->addSuccess($this->__('Block %s was successfully saved', '&quot;'.$model->getBlockTitle().'&quot;'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'tab' => $this->getRequest()->getParam('tab')));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } 
            catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setZBlocksData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $session->addError($this->__('Unable to find a block to save'));
        $this->_redirect('*/*/');
    }

    /*
     * Delete Z-Block Action
     */
    public function deleteAction() 
    {
        if($id = $this->getRequest()->getParam('id'))
        {
            try
            {
                $model = Mage::getModel('zblocks/zblocks')->load($id);

                $title = $model->getBlockTitle();

                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Block %s was successfully deleted', '&quot;'.$title.'&quot;'));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

// = = = = = = = = = = = = = = = = = = = = = content actions section = = = = = = = = = = = = = = = = = = = =

    /*
     * Edit Z-Block content action
     */
    public function editcontentAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $model = Mage::getModel('zblocks/content');
        if($id = $this->getRequest()->getParam('id'))
        {
            $model->load($id);
            if(!$model->getId())
            {
                $session->addError($this->__('This content block no longer exists'));
                $this->_redirectReferer();
                return;
            }
        }

        $data = $model->getData();
        $sessionData = $session->getZBlocksContentData(true);
        if(is_array($sessionData)) $data = array_merge($data, $sessionData);
        $session->setZBlocksContentData(false);

        if($blockId = $this->getRequest()->getParam('block_id'))
            $data['zblock_id'] = $blockId;

        Mage::register('zblocks_content', $data);

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tab_content_edit')->setData('action', $this->getUrl('*/*/saveContent')))
            ->renderLayout();
    }

    /*
     * Save Z-Block content action
     */
    public function saveContentAction() 
    {
        $session = Mage::getSingleton('adminhtml/session');

        if($data = $this->getRequest()->getPost())
        {
            $blockModel = Mage::getModel('zblocks/zblocks')->load($data['zblock_id']);
            if(!$blockModel->getId())
            {
                $session->addError($this->__('Parent block no longer exists'));
                $this->_redirect('*/*/'); 
                return;
            }

            $model = Mage::getModel('zblocks/content');
            try
            {
                $model
                    ->setData($data)
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                $session->addSuccess($this->__('Block %s was successfully saved', '&quot;'.$model->getTitle().'&quot;'));
                // $session->setZBlocksContentData(false);

                if($this->getRequest()->getParam('back'))
                    $this->_redirect('*/*/editcontent', array('id' => $model->getId(), 'block_id' => $data['zblock_id']));
                else
                    $this->_redirect('*/*/edit', array('id' => $data['zblock_id'], 'tab' => 'content'));

                return;
            } 
            catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setZBlocksContentData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $session->addError($this->__('Unable to find a block to save'));
        $this->_redirect('*/*/');
    }

    /*
     * Delete Z-Block content action
     */
    public function deleteContentAction() 
    {
        if($id = $this->getRequest()->getParam('id'))
        {
            try
            {
                $model = Mage::getModel('zblocks/content')->load($id);

                $title = $model->getTitle();
                $zblockId = $model->getZblockId();

                $model->setId($id)->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Block "%s" was successfully deleted', $title));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        if(isset($zblockId)) $this->_redirect('*/*/edit', array('id' => $zblockId, 'tab' => 'content'));
        else $this->_redirect('*/*/');
    }


// = = = = = = = = = = = = = = = = = = = = = AJAX section = = = = = = = = = = = = = = = = = = = =

    /*
     * Dynamic grin renewal through AJAX query action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('zblocks/adminhtml_zblocks_grid')->toHtml()
        );
    }

    /*
     * Content grid action
     */
    public function editGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tab_content_grid')->toHtml()
        );
    }

    /*
     * Categories action
     */
    public function categoriesAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tab_categories')->toHtml()
        );
    }

    /*
     * Category children action
     */
    public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

}
