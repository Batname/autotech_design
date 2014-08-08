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
class Zeon_Manufacturer_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'zeon_manufacturer/general/is_enabled';
    const XML_PATH_DEFAULT_ATTRIBUTE_CODE = 'zeon_manufacturer/frontend/manufacturers_attribute_code';
    const XML_PATH_DEFAULT_META_TITLE = 'zeon_manufacturer/frontend/meta_title';
    const XML_PATH_DEFAULT_META_KEYWORDS = 'zeon_manufacturer/frontend/meta_keywords';
    const XML_PATH_DEFAULT_META_DESCRIPTION = 'zeon_manufacturer/frontend/meta_description';
    
    public function setIsModuleEnabled($value)
    {
        Mage::getModel('core/config')->saveConfig(self::XML_PATH_ENABLED, $value);
    }

    /**
     * Retrieve default title for manufacturers
     *
     * @return string
     */
    public function getManufacturersAttributeCode()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_ATTRIBUTE_CODE);
    }

    /**
     * Retrieve default title for manufacturers
     *
     * @return string
     */
    public function getDefaultTitle()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_TITLE);
    }

    /**
     * Retrieve default meta keywords for manufacturers
     *
     * @return string
     */
    public function getDefaultMetaKeywords()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_KEYWORDS);
    }

    /**
     * Retrieve default meta description for manufacturers
     *
     * @return string
     */
    public function getDefaultMetaDescription()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_DESCRIPTION);
    }
    
    /**
     * Retrieve Template processor for Block Content
     *
     * @return Varien_Filter_Template
     */
    public function getBlockTemplateProcessor()
    {
        return Mage::getModel('zeon_manufacturer/template_filter');
    }
}