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
require_once("Mage/Tag/controllers/ProductController.php");

class Simtech_Searchanise_ProductController extends Mage_Tag_ProductController
{
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
        
    public function listAction()
    {
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            return parent::listAction();
        }
        
        $tagId = $this->getRequest()->getParam('tagId');
        $tag = Mage::getModel('tag/tag')->load($tagId);
        
        if ($tag->getId() && $tag->isAvailableInStore()) {
            if (Mage::helper('searchanise')->checkEnabled()) {
                $block_toolbar = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
                
                Mage::helper('searchanise')->execute(Simtech_Searchanise_Helper_Data::VIEW_TAG, $this, $block_toolbar, $tag);
            }
        }
        
        return parent::listAction();
    }
}