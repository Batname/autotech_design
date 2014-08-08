<?php

class Sitemaster_Checkout_Block_Newsletter extends Mage_Core_Block_Template
{
    /**
     * Convert block to html sting.
     * Checks is possible to show newsletter checkbox
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helper('sitemaster_checkout')->isVisibleNewsletter()
            || Mage::helper('sitemaster_checkout')->isCustomerSubscribed()
        ) {
            return '';
        }

        return parent::_toHtml();
    }
}
