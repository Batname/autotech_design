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
class AW_Zblocks_Block_Adminhtml_Zblocks_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('zblocks_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Block Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'     => $this->__('General Information'),
            'title'     => $this->__('General Information'),
            'content'   => $this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tab_general')->toHtml(),
        ));

        $this->addTab('content_section', array(
            'label'     => $this->__('Content'),
            'title'     => $this->__('Content'),
            'content'   => $this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tab_content')->toHtml(),
        ));

        $this->addTab('schedule_section', array(
            'label'     => $this->__('Schedule'),
            'title'     => $this->__('Schedule'),
            'content'   => $this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tab_schedule')->toHtml(),
        ));

        $this->addTab('categories', array(
            'label'     => $this->__('Categories'),
            'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
            'class'     => 'ajax',
        ));

        $this->addTab('mss', array(
            'label'     => $this->__('Market Segmentation Suite'),
            'title'     => $this->__('Market Segmentation Suite'),
            'content'   => $this->getLayout()->createBlock('zblocks/adminhtml_zblocks_edit_tab_mss')->toHtml(),
        ));

        if($tabName = $this->getRequest()->getParam('tab'))
        {
            $tabName = (strpos($tabName, 'zblocks_tabs_')!==false) // zblocks_tabs_general_section
                        ? substr($tabName, 13)
                        : $tabName.'_section';

            if(isset($this->_tabs[$tabName])) $this->setActiveTab($tabName);
        }

        return parent::_beforeToHtml();
    }
}