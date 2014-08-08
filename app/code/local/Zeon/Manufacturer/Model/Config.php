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

class Zeon_Manufacturer_Model_Config extends Mage_Eav_Model_Config
{
    const XML_PATH_LIST_DEFAULT_SORT_BY     = 'zeon_manufacturer/frontend/default_sort_by';

    protected $_storeId = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('zeon_manufacturer/config');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Zeon_Manufacturer_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id, if is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
            'title'  => Mage::helper('zeon_manufacturer')->__('Manufacturer Title'),
            'update_time'  => Mage::helper('zeon_manufacturer')->__('Recent Manufacturer')
        );

        return $options;
    }

    /**
     * Retrieve resource model
     *
     * @return Zeon_Manufacturer_Model_Resource_Eav_Mysql4_Config
     */
    protected function _getResource()
    {
        return Mage::getResourceModel('zeon_manufacturer/config');
    }

    /**
     * Retrieve Manufacturer List Default Sort By
     *
     * @param mixed $store
     * @return string
     */
    public function getManufacturerListDefaultSortBy($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_LIST_DEFAULT_SORT_BY, $store);
    }
}
