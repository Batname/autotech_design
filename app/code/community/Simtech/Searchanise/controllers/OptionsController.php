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
class Simtech_Searchanise_OptionsController extends Mage_Adminhtml_Controller_Action
{
    const PARAM_USE_NAVIGATION = 'snize_use_navigation';
    const PARAM_USE_FULL_FEED  = 'snize_use_full_feed';
    
    /*
     * options
     */
    public function indexAction()
    {
        $useNavigation = $this->getRequest()->getParam(self::PARAM_USE_NAVIGATION);
        if ($useNavigation != '') {
            Mage::helper('searchanise/ApiSe')->setUseNavigation($useNavigation == 'true' ? true : false);
        }

        $useFullFeed = $this->getRequest()->getParam(self::PARAM_USE_FULL_FEED);
        if ($useFullFeed != '') {
            Mage::helper('searchanise/ApiSe')->setUseFullFeed($useFullFeed == 'true' ? true : false);
        }
        
        exit;
    }
}