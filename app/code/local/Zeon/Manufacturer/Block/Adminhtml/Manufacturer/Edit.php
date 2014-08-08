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

class Zeon_Manufacturer_Block_Adminhtml_Manufacturer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize manufacturer edit page. Set management buttons
     *
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_manufacturer';
        $this->_blockGroup = 'zeon_manufacturer';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('zeon_manufacturer')->__('Save Manufacturer'));
        $this->_updateButton('delete', 'label', Mage::helper('zeon_manufacturer')->__('Delete Manufacturer'));

        $this->_addButton(
            'save_and_edit_button', array(
            'label'   => Mage::helper('zeon_manufacturer')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save'
            ), 100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
            editForm.submit($('edit_form').action + 'back/edit/');}";
    }

    /**
     * Get current loaded manufacturer ID
     *
     */
    public function getManufacturerId()
    {
        return Mage::registry('current_manufacturer')->getId();
    }

    /**
     * Get header text for manufacturer edit page
     *
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_manufacturer')->getId()) {
            return $this->htmlEscape(Mage::registry('current_manufacturer')->getTitle());
        } else {
            return Mage::helper('zeon_manufacturer')->__('New Manufacturer');
        }
    }

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}