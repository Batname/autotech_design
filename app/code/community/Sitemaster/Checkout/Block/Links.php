<?php

class Sitemaster_Checkout_Block_Links extends Mage_Core_Block_Abstract
{

    /**
     * Replace link to standard checkout with one that refers to the cart
     *
     * @return Sitemaster_Checkout_Block_Links
     */
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->getBlock('top.links');
        if ($block) {
          $block->removeLinkByUrl($this->getUrl('checkout'));
          $text = $this->helper('checkout')->__('Checkout');
          $block->addLink($text, 'checkout/cart', $text, true, array('_secure' => true), 60, null, 'class="top-link-checkout"');
        }
        return $this;
    }
}