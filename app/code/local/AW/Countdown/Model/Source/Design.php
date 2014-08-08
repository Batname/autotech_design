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
 * @package    AW_Countdown
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Countdown_Model_Source_Design extends AW_Countdown_Model_Source_Abstract {

    public function toOptionArray() {
        $package = Mage_Core_Model_Design_Package::DEFAULT_PACKAGE;
        if (AW_All_Helper_Versions::getPlatform() == 100) {
            $package = 'enterprise';
        }
        if (AW_All_Helper_Versions::getPlatform() == 10) {
            $package = 'pro';
        }
        $dir = Mage::getDesign()->getSkinBaseDir(array('_area' => Mage_Core_Model_Design_Package::DEFAULT_AREA, '_default' => true, '_theme' => Mage_Core_Model_Design_Package::DEFAULT_THEME, '_package' => $package)) . DS . 'aw_countdown';
        $scopes = $this->_listDirectories($dir);
        return $scopes;
    }

    private function _listDirectories($path, $fullPath = false) {
        $result = array();
        $dir = opendir($path);
        if ($dir) {
            while ($entry = readdir($dir)) {
                if (substr($entry, 0, 1) == '.' || !is_dir($path . DS . $entry)) {
                    continue;
                }
                if ($fullPath) {
                    $entry = $path . DS . $entry;
                }
                $result[$entry] = $entry;
            }
            unset($entry);
            closedir($dir);
        }

        return $result;
    }

}
