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
class AW_Ajaxcartpro_Model_Observer{

	public function addToCartEvent($observer){
        $request = Mage::app()->getFrontController()->getRequest();
        if ( !$request->getParam('in_cart') && !$request->getParam('is_checkout')
            && $request->getParam('awacp') )
		{
			if ($request->getParam('awwishl'))
            {
                $cart = Mage::helper('ajaxcartpro')->renderCart();
                $wishlist = Mage::helper('ajaxcartpro')->renderWishlist();
                $cartTopLinks = Mage::helper('ajaxcartpro')->renderTopCartLinkTitle();
                $wishlistTopLinks = Mage::helper('ajaxcartpro')->renderWishlistTopLinks();
                Mage::helper('ajaxcartpro')->sendWishlistResponse($cart, $cartTopLinks, $wishlist, $wishlistTopLinks);
            }
            else
            {
                $cart = Mage::helper('ajaxcartpro')->renderCart();
                $text = Mage::helper('ajaxcartpro')->renderTopCartLinkTitle();
                Mage::helper('ajaxcartpro')->sendResponse($cart, $text);
            }
		}

		if ( $request->getParam('is_checkout')	)
		{
			$cart = Mage::helper('ajaxcartpro')->renderBigCart();
			$text = Mage::helper('ajaxcartpro')->renderTopCartLinkTitle();
			Mage::helper('ajaxcartpro')->sendResponse($cart, $text);
		}

	}
	public function diea(){
		die();
	}
}
?>
