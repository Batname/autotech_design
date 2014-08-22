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
require_once("Mage/Catalog/controllers/CategoryController.php");

class Simtech_Searchanise_CategoryController extends Mage_Catalog_CategoryController
{
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
        
    protected function _getCurCategory()
    {
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        
        if (!$categoryId) { 
            return null; 
        }
        
        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
        
        if (!Mage::helper('catalog/category')->canShow($category)) { 
            return null; 
        }
        
        return $category;
    }
    
    /**
     * Category view action
     */
    public function viewAction()
    {
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            return parent::viewAction();
        }
        
        if ($category = $this->_getCurCategory()) {
            // If you need a this check, please, uncomment
            //~ $is_anchor = $category->getData('is_anchor');
            //~ 
            //~ if ($is_anchor == 'Y')
            {
                $display_mode = $category->getData('display_mode');

                // This check not need
                // if (($display_mode == 'PRODUCTS') || ($display_mode == 'PRODUCTS_AND_PAGE')) 
                {
                    if (Mage::helper('searchanise')->checkEnabled()) {
                        $block_toolbar = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
                        
                        Mage::helper('searchanise')->execute(Simtech_Searchanise_Helper_Data::VIEW_CATEGORY, $this, $block_toolbar, $category);
                    }
                }
            }
        }
        
        return parent::viewAction();
    }
}