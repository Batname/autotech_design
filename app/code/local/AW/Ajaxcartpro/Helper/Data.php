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
class AW_Ajaxcartpro_Helper_Data extends Mage_Core_Helper_Abstract {

    protected static $instance;

    public static function getInstance()
    {
        if (!self::$instance)
        {
            if (preg_match('/^(1.7|1.8|1.9)/', Mage::getVersion()))
                self::$instance = Mage::helper('ajaxcartpro/data_mag18');
            else
                self::$instance = Mage::helper('ajaxcartpro/data_mag14');
        }
        return self::$instance;
    }

    /**
     * Return wishlist rendered HTML
     * @return string
     */
    public function renderWishlist() {
        return  Mage::getSingleton('core/layout')
                ->createBlock('wishlist/customer_wishlist')
                ->setTemplate('wishlist/view.phtml')
                ->renderView();
    }

    /**
     * Return wishlist top links rendered text
     * @return string
     */
    public function renderWishlistTopLinks() {
        $count = Mage::helper('wishlist')->getItemCount();
        if( $count == 1 ) {
            $text = Mage::helper('checkout')->__('My Wishlist (%s item)', $count);
        } elseif( $count > 0 ) {
            $text = Mage::helper('checkout')->__('My Wishlist (%s items)', $count);
        } else {
            $text = Mage::helper('checkout')->__('My Wishlist');
        }
        return $text;
    }

    /**
     * Return small cart rendered HTML
     * @return string
     */
    public function renderCart() {
        return self::getInstance()->renderCart();
    }

    /**
     * Render big cart and return its HTML
     * @return string
     */
    public function renderBigCart() {

        $L = Mage::getSingleton('core/layout');


        $totals = $L
                ->createBlock('checkout/cart_totals')
                ->setTemplate('checkout/cart/totals.phtml')
        ;
        $shipping = $L
                ->createBlock('checkout/cart_shipping')
                ->setTemplate('checkout/cart/shipping.phtml')
        ;

        $coupon = $L
                ->createBlock('checkout/cart_coupon')
                ->setTemplate('checkout/cart/coupon.phtml')
        ;

        // top methods

        $t_onepage = $L
                ->createBlock('checkout/onepage_link')
                ->setTemplate('checkout/onepage/link.phtml')
        ;
        $t_methods = $L
                ->createBlock('core/text_list')
                ->append($t_onepage, 'top_methods');


        //methods
        $onepage = $L
                ->createBlock('checkout/onepage_link')
                ->setTemplate('checkout/onepage/link.phtml')
        ;

        $multishipping = $L
                ->createBlock('checkout/multishipping_link')
                ->setTemplate('checkout/multishipping/link.phtml')
        ;




        $methods = 	$L
                ->createBlock('core/text_list')
                ->append($onepage, "onepage")
                ->append($multishipping, "multishipping");


        // Cross-sales etc

        $crossel = $L
                ->createBlock('checkout/cart_crosssell')
                ->setTemplate('checkout/cart/crosssell.phtml')
        ;


        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

        $cart1 = $L
                ->createBlock('checkout/cart')
                ->setEmptyTemplate('checkout/cart/noItems.phtml')
                ->setCartTemplate('checkout/cart.phtml')
                ->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/item/default.phtml')
                ->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/item/default.phtml')
                ->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/item/default.phtml')
                ->addItemRender('downloadable', 'downloadable/checkout_cart_item_renderer', 'downloadable/checkout/cart/item/default.phtml')
                ->addItemRender('bundle', 'bundle/checkout_cart_item_renderer', 'checkout/cart/item/default.phtml')
                ->addItemRender('subscription_simple', 'sarp/checkout_cart_item_renderer_simple', 'checkout/cart/item/default.phtml')
                ->addItemRender('bookable', 'booking/checkout_cart_item_renderer', 'checkout/cart/item/default.phtml')
                ->setTemplate('checkout/cart.phtml')
                ->setChild('top_methods',$t_methods)
                ->setChild('totals', $totals)
                ->setChild('shipping', $shipping)
                ->setChild('coupon', $coupon)
                ->setChild('methods', $methods)
                ->setChild('crosssell', $crossel)
        ;
        $cart1
                ->chooseTemplate();
        return $cart1
                ->renderView();
    }

    /**
     * Return top link with cart items
     * @return string
     */
    public function renderTopCartLinkTitle() {
        return self::getInstance()->renderTopCartLinkTitle();
    }

    /**
     * Send AJAX response and exit
     * @param string $cart
     * @param string $text
     */
    public function sendResponse($cart, $text, $wishlist = null, $wishlistLinks = null) {
        header('content-type: text/javascript');

        $obj = array(
                'r' => 'success',
                'cart' => $cart,
                'links' => $text,
                'wishlist' => $wishlist,
                'wishlist_links' => $wishlistLinks
        );
        Zend_Json::$useBuiltinEncoderDecoder = true;
       Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
       Mage::app()->getFrontController()->getResponse()
            ->setBody(Zend_Json::encode($obj));
    }

    /**
     * Send AJAX response and exit
     * @param string $cart
     * @param string $text
     */
    public function sendWishlistResponse($cart, $text, $wishlist = null, $wishlistLinks = null) {
        header('content-type: text/javascript');

        $obj = array(
                'r' => 'success',
                'cart' => $cart,
                'links' => $text,
                'wishlist' => $wishlist,
                'wishlist_links' => $wishlistLinks
        );
        Zend_Json::$useBuiltinEncoderDecoder = true;
       echo Zend_Json::encode($obj);
       die;
    }

    public function displayCountdown()
    {
        $countdown = Mage::getStoreConfig('ajaxcartpro/general/countdown');
        $html = '';
        if ((int)$countdown && $countdown>0)
            $html = ' (<span id="ACPcountdown">'.$countdown.'</span>)';
        return $html;
    }
}
