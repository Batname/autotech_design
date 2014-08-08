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
class AW_Zblocks_Block_Adminhtml_Zblocks_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('zblocks_schedule', array('legend' => $this->__('General Information')));

        $fieldset->addField('block_title', 'text', array(
            'name'      => 'block_title',
            'label'     => $this->__('Block Title'),
            'title'     => $this->__('Block Title'),
        ));

        $fieldset->addField('creation_time', 'hidden', array(
                'name'      => 'creation_time',
                'value'     => '', //isset($data['creation_time']) ? $data['creation_time'] : '',
            ));

        if(Mage::app()->isSingleStoreMode())
        {
            $fieldset->addField('store_ids', 'hidden', array(
                'name'      => 'store_ids[]',
                'value'     => Mage::app()->getStore()->getId(),
            ));
        }
        else {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name'      => 'store_ids[]',
                'label'     => $this->__('Store View'),
                'title'     => $this->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }

        $fieldset->addField('block_is_active', 'select', array(
            'label'     => $this->__('Status'),
            'title'     => $this->__('Status'),
            'name'      => 'block_is_active',
            'options'   => array(
                '1' => $this->__('Enabled'),
                '0' => $this->__('Disabled'),
            ),
        ));

        $fieldset->addField('block_position', 'select', array(
            'label'     => $this->__('Block Position'),
            'title'     => $this->__('Block Position'),
            'name'      => 'block_position',
            'values'    => Mage::helper('zblocks')->getBlockIdsToOptionsArray(),
        ));

        $fieldset->addField('block_position_custom', 'text', array(
            'name'      => 'block_position_custom',
            'label'     => $this->__('Custom Position'),
            'title'     => $this->__('Custom Position'),
            'note' => $this->__("Required if Block Position is set to 'Custom'"),
        ));

        $fieldset->addField('show_in_subcategories', 'select', array(
            'label'     => $this->__('Show in subcategories'),
            'title'     => $this->__('Show in subcategories'),
            'name'      => 'show_in_subcategories',
            'note'      => $this->__('Show in subcategories and products'),
            'options'   => array(
                '1' => $this->__('Enabled'),
                '0' => $this->__('Disabled'),
            ),
        ));

        $fieldset->addField('rotator_mode', 'select', array(
            'label'     => $this->__('Mode'),
            'title'     => $this->__('Mode'),
            'name'      => 'rotator_mode',
            'options'   => Mage::helper('zblocks')->getRotatorModesToOptionsArray(),
        ));

        $fieldset->addField('block_sort_order', 'text', array(
            'name' => 'block_sort_order',
            'label' => $this->__('Sort Order'),
            'title' => $this->__('Sort Order'),
        ));

        if($data = Mage::registry('zblocks_data')) $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
