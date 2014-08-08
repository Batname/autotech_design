<?php
/**
 * Zeon Solutions, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Zeon Solutions License
 * that is bundled with this package in the file LICENSE_ZE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zeonsolutions.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.zeonsolutions.com for more information.
 *
 * @category    Zeon
 * @package     Zeon_Manufacturer
 * @copyright   Copyright (c) 2012 Zeon Solutions, Inc. All Rights Reserved.(http://www.zeonsolutions.com)
 * @license     http://www.zeonsolutions.com/license/
 */

class Zeon_Manufacturer_Block_Adminhtml_Manufacturer_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('zeon_manufacturer')->__('Meta Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Set form id prefix, set values if manufacturer is editing
     *
     * @return Zeon_Manufacturer_Block_Adminhtml_Manufacturer_Edit_Tab_Meta
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $htmlIdPrefix = 'manufacturer_meta_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $fieldsetHtmlClass = 'fieldset-wide';

        $model = Mage::registry('current_manufacturer');

        Mage::dispatchEvent('adminhtml_manufacturer_edit_tab_meta_before_prepare_form',
            array('model' => $model, 'form' => $form)
        );

        // add meta information fieldset
        $fieldset = $form->addFieldset('default_fieldset', array(
            'legend' => Mage::helper('zeon_manufacturer')->__('Meta Information'),
            'class'  => $fieldsetHtmlClass,
        ));

       $fieldset->addField('meta_keywords', 'textarea', array(
            'name'     => 'meta_keywords',
            'label'    => Mage::helper('zeon_manufacturer')->__('Meta Keywords'),
            'disabled' => (bool)$model->getIsReadonly(),
       ));

       $fieldset->addField('meta_description', 'textarea', array(
            'name'     => 'meta_description',
            'label'    => Mage::helper('zeon_manufacturer')->__('Meta Description'),
            'disabled' => (bool)$model->getIsReadonly(),
       ));

       $form->setValues($model->getData());
       $this->setForm($form);
       return parent::_prepareForm();
    }
}