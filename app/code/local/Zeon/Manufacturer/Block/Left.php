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
class Zeon_Manufacturer_Block_Left extends Mage_Core_Block_Template
{
    protected $_manufacturersCollection;

    /**
     * Retrieve Manufacturers collection
     *
     * @return Zeon_Manufacturer_Model_Resource_Manufacturer_Collection
     */
    protected function _getManufacturersCollection()
    {
        if (is_null($this->_manufacturersCollection)) {
            $this->_manufacturersCollection = Mage::getResourceModel('zeon_manufacturer/manufacturer_collection')
									->distinct(true)
                                    ->addStoreFilter(Mage::app()->getStore()->getId())
                                    ->addFieldToFilter('status', Zeon_Manufacturer_Model_Status::STATUS_ENABLED)
                                    ->addOrder('sort_order', 'asc');
        }
        return $this->_manufacturersCollection;
    }

    /**
     * Retrieve loaded Manufacturers collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getManufacturersCollection()
    {
        return $this->_getManufacturersCollection();
    }
}