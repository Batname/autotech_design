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

class Simtech_Searchanise_Block_Product_Result extends Mage_Tag_Block_Product_Result
{
    protected function _getProductCollection()
    {
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            return parent::_getProductCollection();
        }

        if (is_null($this->_productCollection)) {
            $tagModel = Mage::getModel('tag/tag');
            $collection = $tagModel->getEntityCollection();
            
            if (method_exists($collection, 'setSearchaniseRequest')) {
                $collection->setSearchaniseRequest(Mage::helper('searchanise')->getSearchaniseRequest());
            }
            
            if ((!method_exists($collection, 'checkSearchaniseResult')) || (!$collection->checkSearchaniseResult())) {
                return parent::_getProductCollection();
            }
            
            $this->_productCollection = $collection;
            $this->_productCollection
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addSearchaniseFilter()
                ->addTagFilter($this->getTag()->getId())
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->setActiveFilter();

            Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($this->_productCollection);
        }
        
        return $this->_productCollection;
    }
}