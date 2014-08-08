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
class AW_Zblocks_Block_Adminhtml_Zblocks_Edit_Tab_Schedule extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('zblocks_activateOn', array('legend'=>$this->__('Display block:')));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('schedule_from_date', 'date', array(
            'name'   => 'schedule_from_date',
            'label'  => $this->__('From Date'),
            'title'  => $this->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
        ));

        $fieldset->addField('schedule_to_date', 'date', array(
            'name'   => 'schedule_to_date',
            'label'  => $this->__('To Date'),
            'title'  => $this->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
        ));

        $fieldset = $form->addFieldset('zblocks_schedule', array('legend'=>$this->__('Schedule Pattern')));

        $__patternsArray = Mage::helper('zblocks')->getPatternsToOptionsArray();
        if (!is_array($__patternsArray)) $__patternsArray = array();
        $fieldset->addField('schedule_pattern', 'select', array(
            'name'      => 'schedule_pattern',
            'label'     => $this->__('Show'),
            'title'     => $this->__('Show'),
            'default'   => current($__patternsArray),
            'options'   => Mage::helper('zblocks')->getPatternsToOptionsArray(),
        ));

        $fieldset->addField('schedule_from_time', 'text', array(
            'name'      => 'schedule_from_time',
            'label'     => $this->__('From Time'),
            'title'     => $this->__('From Time'),
            'note' => $this->__('As Hours:Minutes:Seconds'),
        ));

        $fieldset->addField('schedule_to_time', 'text', array(
            'name'      => 'schedule_to_time',
            'label'     => $this->__('To Time'),
            'title'     => $this->__('To Time'),
            'note' => $this->__('As Hours:Minutes:Seconds'),
        ));

        if($data = Mage::registry('zblocks_data')) $form->setValues($data);

        return parent::_prepareForm();
    }
}