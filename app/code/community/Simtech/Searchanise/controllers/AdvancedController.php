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
require_once("Mage/CatalogSearch/controllers/AdvancedController.php");

class Simtech_Searchanise_AdvancedController extends Mage_CatalogSearch_AdvancedController
{
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
        
    /**
     * Display search result
     */
    public function resultAction()
    {
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            return parent::resultAction();
        }

        try {
            $query = $this->getRequest()->getQuery();
        } catch (Mage_Core_Exception $e) {
            return parent::resultAction();
        }

        if ($query) {
            if (Mage::helper('searchanise')->checkEnabled()) {
                $block_toolbar = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
                
                Mage::helper('searchanise')->execute(Simtech_Searchanise_Helper_Data::TEXT_ADVANCED_FIND, $this, $block_toolbar, $query);
            }
        }
        
        return parent::resultAction();
    }
}