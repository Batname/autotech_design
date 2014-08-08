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
class AW_Zblocks_Block_Adminhtml_Zblocks_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'zblocks';
        $this->_controller = 'adminhtml_zblocks';
        
        $this->_updateButton('save', 'label', Mage::helper('zblocks')->__('Save Block'));
        $this->_updateButton('delete', 'label', Mage::helper('zblocks')->__('Delete Block'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('zblocks_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'zblocks_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'zblocks_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/tab/'+zblocks_tabsJsTabs.activeTab.id);
            }
        ";
    }

    public function getHeaderText()
    {
        $data = Mage::registry('zblocks_data');
        return isset($data['block_title'])
            ? Mage::helper('zblocks')->__('Edit Block \'%s\'', $this->htmlEscape($data['block_title']))
            : Mage::helper('zblocks')->__('Add Block');
    }
}