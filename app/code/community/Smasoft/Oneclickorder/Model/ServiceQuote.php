<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Smasoft
 * @package     Smasoft_Oneclikorder
 * @copyright   Copyright (c) 2013 Slabko Michail. <l.nagash@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Smasoft_Oneclickorder_Model_ServiceQuote extends Mage_Sales_Model_Service_Quote
{

    protected function _validate()
    {
        if (Mage::registry('oneclickorder_ignore_quote_validation')) {
            return $this;
        }
        return parent::_validate();
    }
}