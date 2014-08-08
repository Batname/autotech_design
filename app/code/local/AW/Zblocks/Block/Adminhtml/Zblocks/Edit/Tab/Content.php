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
class AW_Zblocks_Block_Adminhtml_Zblocks_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_zblocks_edit_tab_content';
        $this->_blockGroup = 'zblocks';
        $this->_headerText = $this->__('Content Manager');
        $this->_addButtonLabel = $this->__('Add Item');

        parent::__construct();

        $this->_buttons[0]['add']['onclick'] = $this->getRequest()->getParam('id')
            ?('setLocation(\''.$this->getUrl('*/*/editcontent', array('block_id' => $this->getRequest()->getParam('id'))).'\')')
            :('alert(\''.$this->__('Please save this block before adding content items. Press &quot;Save And Continue Edit&quot; button to save the block.').'\')');
    }
}