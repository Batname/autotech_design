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

class Simtech_Searchanise_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    /**
    * Get price range for building filter steps
    *
    * @return int
    */
    public function getPriceRange()
    {
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            return parent::getPriceRange();
        }

        $collection = $this->getLayer()->getProductCollection();

        if ((!method_exists($collection, 'checkSearchaniseResult')) || (!$collection->checkSearchaniseResult())) {
            return parent::getPriceRange();
        }             

        $newRange = $collection
               ->getSearchaniseRequest()
               ->getPriceRangeFromAttribute($this->getAttributeModel());
        if (!$newRange) {
            return parent::getPriceRange();
        }
        
        $rate = Mage::app()->getStore()->getCurrentCurrencyRate();

        if ((!$rate) || ($rate == 1)) {
            // nothing
        } else {
            $newRange *= $rate;
        }

        return $newRange;
    }
}
