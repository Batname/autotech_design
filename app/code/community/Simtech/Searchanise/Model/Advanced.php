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

class Simtech_Searchanise_Model_Advanced extends Mage_CatalogSearch_Model_Advanced
{
    /**
     * Add advanced search filters to product collection
     *
     * @param   array $values
     * @return  Mage_CatalogSearch_Model_Advanced
     */
    public function addFilters($values)
    {
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            return parent::addFilters($values);
        }
        $collection = $this->getProductCollection();
     
        if (!$collection && !method_exists($collection, 'checkSearchaniseResult') || !$collection->checkSearchaniseResult()) {
            return parent::addFilters($values);
        }
        // Nothing,
        
        return $this;
    }

    /**
     * Prepare product collection
     *
     * @param Mage_CatalogSearch_Model_Resource_Advanced_Collection $collection
     * @return Mage_Catalog_Model_Layer
    */
    public function prepareProductCollection($collection)
    {
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            return parent::prepareProductCollection($collection);
        }
        
        if (method_exists($collection, 'setSearchaniseRequest')) {
            $collection->setSearchaniseRequest(Mage::helper('searchanise')->getSearchaniseRequest());
        }
     
        if (!$collection && !method_exists($collection, 'checkSearchaniseResult') || !$collection->checkSearchaniseResult()) {
            return parent::prepareProductCollection($collection);
        }

        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addSearchaniseFilter()
            ->setStore(Mage::app()->getStore())
            ->addMinimalPrice()
            ->addTaxPercents()
            ->addStoreFilter();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);
        
        return $this;
    }
}
