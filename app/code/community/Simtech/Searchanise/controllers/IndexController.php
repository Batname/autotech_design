<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/
class Simtech_Searchanise_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->_setActiveMenu('searchanise/index/index');
        
        return $this;
    }
    
    /*
     * dashboard
     */
    public function indexAction()
    {
        $this->loadLayout();
        
        $this->_addContent($this->getLayout()->createBlock('core/text', 'inner-wrap-start')->setText('<div id="searchanise-settings-wrapper">'));
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('searchanise/dashboard.phtml'));
        
        $this->_addContent($this->getLayout()->createBlock('core/text', 'inner-wrap-end')->setText('</div>'));
        
        $this->renderLayout();
    }
    
    public function termsAction()
    {
        // TODO: add wrapper for non-ajax requests - LOW priority
        // return terms text pulled from server
        print $this->getLayout()->createBlock("searchanise/Adminhtml_Index_Terms")->toHtml();
        exit();
    }
}