<?php
/**
 * Zeon Solutions, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Zeon Solutions License
 * that is bundled with this package in the file LICENSE_ZE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zeonsolutions.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.zeonsolutions.com for more information.
 *
 * @category    Zeon
 * @package     Zeon_Manufacturer
 * @copyright   Copyright (c) 2012 Zeon Solutions, Inc. All Rights Reserved.(http://www.zeonsolutions.com)
 * @license     http://www.zeonsolutions.com/license/
 */

class Zeon_Manufacturer_Model_Options extends Mage_Core_Model_Abstract
{
    public function getManufacturers()
    {
        $attributeCode = Mage::helper('zeon_manufacturer')->getManufacturersAttributeCode();
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', "$attributeCode");
        foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
             $attributeArray[$option['value']] = $option['label'];
        }
        return($attributeArray);
    }
}