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
 * @package    AW_Ajaxcartpro
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Ajaxcartpro_CartController extends Mage_Core_Controller_Front_Action {
    public function removeAction() {
        $id =  $this->getRequest()->getParam('id');
        Mage::getSingleton('checkout/cart')->removeItem($id)->save();
        if(!$this->getRequest()->getParam('is_checkout')) {
            $cart = Mage::helper('ajaxcartpro')->renderCart();
        }else {
            $cart = '';
            //if ($_SERVER['REMOTE_ADDR']==''){ //test mode on
            $L = Mage::getSingleton('core/layout');

            $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
            if( isset($modules['AW_Checkoutpromo']) ) {
                $appliedBlockIds = Mage::helper('checkoutpromo')->getAppliedBlockIds();
                if( is_array($appliedBlockIds) && array_key_exists('shoppingcartpromo', $appliedBlockIds)) {
                    foreach($appliedBlockIds['shoppingcartpromo'] as $appliedBlockId) {
                        $cart .= $L->createBlock('cms/block')->setBlockId($appliedBlockId)->toHtml();
                    }
                }
                //} //test mode on
            }
            $cart = Mage::helper('ajaxcartpro')->renderBigCart();
        }
        $text = Mage::helper('ajaxcartpro')->renderTopCartLinkTitle();
        Mage::helper('ajaxcartpro')->sendResponse($cart, $text);
    }
}
