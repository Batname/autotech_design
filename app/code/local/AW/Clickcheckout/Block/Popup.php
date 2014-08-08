<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Clickcheckout
 * @version    1.1.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Clickcheckout_Block_Popup extends Mage_Core_Block_Template
{

    public function checkoutEnabled()
    {
        return Mage::helper('awclickcheckout')->canOnePage();
    }

    public function quoteHasErrors(){
        $model=Mage::getModel('awclickcheckout/oneclick');
        $quote=$model->getOnepage()->getQuote();
        if(!$quote->hasItems()){
            return false;
        }
        if ($quote->getHasError()) {
            return true;
        }

        if (!$quote->validateMinimumAmount()) {
            return true;
        }

        $items=$quote->getAllItems();
        foreach($items as $item){
            $salable = $item->getProduct()->isSalable();
            if(!$salable){
                            return true;
            }
        }

        return false;
    }
}