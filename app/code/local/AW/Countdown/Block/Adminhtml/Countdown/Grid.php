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


class AW_Countdown_Block_Adminhtml_Countdown_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('countdownid');
        $this->setDefaultSort('countdownid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('awcountdown/countdown')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $helper = Mage::helper('awcountdown');

        $this->addColumn('countdownid', array(
            'header' => $helper->__('ID'),
            'align' => 'right',
            'width' => '5',
            'index' => 'countdownid'
        ));

        $this->addColumn('name', array(
            'header' => $helper->__('Block title'),
            'align' => 'left',
            'index' => 'name'
        ));

        $this->addColumn('is_enabled', array(
            'header' => $helper->__('Status'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'is_enabled',
            'type' => 'options',
            'options' => array(
                AW_Countdown_Model_Source_Status::ENABLED => $helper->__(AW_Countdown_Model_Source_Status::ENABLED_LABEL),
                AW_Countdown_Model_Source_Status::DISABLED => $helper->__(AW_Countdown_Model_Source_Status::DISABLED_LABEL)
            )
        ));
        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $this->addColumn('date_from', array(
            'header' => $helper->__('Active from'),
            'index' => 'date_from',
            'type' => 'date',
            'format' => $outputFormat,
            'time' => true
        ));

        $this->addColumn('date_to', array(
            'header' => $helper->__('Active to'),
            'index' => 'date_to',
            'type' => 'date',
            'format' => $outputFormat,
            'time' => true
        ));


        $this->addColumn('url', array(
            'header' => $helper->__('URL'),
            'align' => 'left',
            'index' => 'url'
        ));

        $this->addColumn('design', array(
            'header' => $helper->__('Design'),
            'align' => 'left',
            'index' => 'design',
            'type' => 'options',
            'options' => Mage::getModel('awcountdown/source_design')->toOptionArray()
        ));

        $this->addColumn('autom_display', array(
            'header' => $helper->__('Automation'),
            'align' => 'center',
            'width' => '160',
            'index' => 'autom_display',
            'type' => 'options',
            'options' => Mage::getSingleton('awcountdown/source_automation')->getOptionArray(),
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Running Status'),
            'align' => 'center',
            'width' => '160',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('awcountdown/source_running')->toOptionArray(),
        ));
        parent::_prepareColumns();
        return $this;
    }

    protected function _prepareMassaction() {

        $this->setMassactionIdField('countdownid');
        $this->getMassactionBlock()->setFormFieldName('countdownid');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('awcountdown')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('awcountdown')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('awcountdown/source_status')->toOptionArray();

        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('awcountdown')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('awcountdown')->__('Status'),
                    'values' => $statuses
                )
            ),
            'confirm' => Mage::helper('awcountdown')->__('Are you sure?'),
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit/', array('countdownid' => $row->getCountdownid()));
    }

}
