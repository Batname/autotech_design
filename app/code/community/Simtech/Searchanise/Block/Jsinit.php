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

class Simtech_Searchanise_Block_Jsinit extends Mage_Core_Block_Text
{
    protected function _toHtml()
    {
        $html = '';
        $store = Mage::app()->getStore();
        
        if (!Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true, $store)) {
            return $html;
        }

        $apiKey = Mage::helper('searchanise/ApiSe')->getApiKey();
        
        if (empty($apiKey)) {
            return $html;
        }
                
        $inputId = Mage::helper('searchanise/ApiSe')->getInputIdSearch();
        if ($inputId == '') {
            // Uncomment the lines below if it is necessary to disable search widget in frontend
            //~ return '';
        }
        if (empty($inputId)) {
            $inputId = 'search';
        }
        $union = 'Searchanise.AutoCmpParams.union = {};';
        $restrictBy = '';

        $seServiceUrl = Mage::helper('searchanise/ApiSe')->getServiceUrl();
        $priceFormat = Mage::helper('searchanise/ApiSe')->getPriceFormat($store);
        $searchWidgetsLink = Mage::helper('searchanise/ApiSe')->getSearchWidgetsLink(false);

        $union .= " Searchanise.AutoCmpParams.union.price = {};";
        $union .= " Searchanise.AutoCmpParams.union.price.min = '" . Mage::helper('searchanise/ApiSe')->getCurLabelForPricesUsergroup() . "';";

        $minQuantityDecimals = Mage::helper('searchanise/ApiSe')->getMinQuantityDecimals();
        if (!empty($minQuantityDecimals)) {
            $restrictBy .= "Searchanise.AutoCmpParams.restrictBy.quantity_decimals = '{$minQuantityDecimals},';";
        }
        
        $showOutOfStock = Mage::getStoreConfigFlag(Mage_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK);
        if ($showOutOfStock) {
            // nothing
        } else {
            $restrictBy .= "\nSearchanise.AutoCmpParams.restrictBy.is_in_stock = '1';";
        }

        $priceFormat['after'] = $priceFormat['after'] ? 'true' : 'false';
        
        $html .= 
            "<script type=\"text/javascript\">
            //<![CDATA[
                Searchanise = {};
                Searchanise.host        = '{$seServiceUrl}';
                Searchanise.api_key     = '{$apiKey}';
                Searchanise.SearchInput = '#{$inputId}';
                
                Searchanise.AutoCmpParams = {};
                {$union}
                Searchanise.AutoCmpParams.restrictBy = {};
                Searchanise.AutoCmpParams.restrictBy.status = '1';
                Searchanise.AutoCmpParams.restrictBy.visibility = '3|4';
                {$restrictBy}
                
                Searchanise.options = {};
                Searchanise.options.LabelSuggestions = 'Popular suggestions';
                Searchanise.options.LabelProducts = 'Products';
                Searchanise.AdditionalSearchInputs = '#name,#description,#sku';

                Searchanise.options.PriceFormat = {
                    decimals_separator:  '" . addslashes($priceFormat['decimals_separator']) . "',
                    thousands_separator: '" . addslashes($priceFormat['thousands_separator']) . "',
                    symbol:              '" . addslashes($priceFormat['symbol']) . "',

                    decimals: '{$priceFormat['decimals']}',
                    rate:     '{$priceFormat['rate']}',
                    after:     {$priceFormat['after']}
                };
                
                (function() {
                    var __se = document.createElement('script');
                    __se.src = '{$searchWidgetsLink}';
                    __se.setAttribute('async', 'true');
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(__se, s);
                })();
            //]]>
            </script>";
        
        // Uncomment the lines below if it is necessary to hide price in search widget
        // $html .= '
        //     <style type="text/css">
        //         .snize-price {
        //             display: none !important;
        //         }
        //     </style>';

        // Uncomment the lines below if it is necessary to fix size images in widget
        // $html .= '
        //     <style type="text/css">
        //         .snize-item-image {
        //             max-width: 70px !important;
        //         }
        //     </style>';

        return $html;
    }
}