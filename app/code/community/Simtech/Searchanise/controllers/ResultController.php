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
require_once("Mage/CatalogSearch/controllers/ResultController.php");

class Simtech_Searchanise_ResultController extends Mage_CatalogSearch_ResultController
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
    
    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    protected function _getToolbarBlock()
    {
        $blockName = 'product_list_toolbar';
        if ($blockName) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }
    
    /**
     * Display search result
     */
    public function indexAction()
    {
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            return parent::indexAction();
        }
        
        $query = Mage::helper('catalogsearch')->getQuery();
        /* @var $query Mage_CatalogSearch_Model_Query */

        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            if (Mage::helper('searchanise')->checkEnabled()) {
                $blockToolbar = $this->_getToolbarBlock();
                                
                Mage::helper('searchanise')->execute(Simtech_Searchanise_Helper_Data::TEXT_FIND, $this, $blockToolbar, $query);
            }
        }
        
        return parent::indexAction();
    }
}