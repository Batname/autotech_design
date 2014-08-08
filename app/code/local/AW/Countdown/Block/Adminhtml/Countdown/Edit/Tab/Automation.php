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


class AW_Countdown_Block_Adminhtml_Countdown_Edit_Tab_Automation extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $model = Mage::registry('countdown_data');
        /* if($model){
          $model->_conditions->setJsFormObject('awcountdown_conditions_fieldset');
          } */
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('auto_');
        $helper = Mage::helper('awcountdown');
        $automation_fieldset = $form->addFieldset('design_design', array(
            'legend' => $this->__('Automation')
                ));
        $automation_fieldset->addField('autom_display', 'select', array(
            'name' => 'autom_display',
            'label' => $this->__('Display automatically'),
            'title' => $this->__('Display automatically'),
            'required' => true,
            'values' => Mage::getModel('awcountdown/source_automation')->getOptionArray()
        ));
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('*/adminhtml_countdown/newConditionHtml/form/auto_conditions_fieldset'));

        $conditions_fieldset = $form->addFieldset('conditions_fieldset', array(
                    'legend' => $this->__('Conditions')
                ))->setRenderer($renderer);

        $conditions_fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
            'required' => false,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}