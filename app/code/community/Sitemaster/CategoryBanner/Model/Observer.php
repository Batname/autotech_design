<?php

class Sitemaster_CategoryBanner_Model_Observer
{
    public function catalogProductLoadAfter(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('sitemaster_categorybanner/general/active')) {
            $category = $observer->getCategory();
            $maincat = explode("/", $category->getPath());
            if ($maincat[2]) {
                switch ($maincat[2]) {
                    case 32:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menumasla_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menumasla_staticblockid'));
                        }
                        break;
                    case 149:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menuorigin_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menuorigin_staticblockid'));
                        }
                        break;
                    case 33:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menusmazki_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menusmazki_staticblockid'));
                        }
                        break;
                    case 34:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menufiltra_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menufiltra_staticblockid'));
                        }
                        break;
                    case 35:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menuautohimia_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menuautohimia_staticblockid'));
                        }
                        break;
                    case 36:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menuautocosm_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menuautocosm_staticblockid'));
                        }
                        break;
                    case 37:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menuzapchasti_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menuzapchasti_staticblockid'));
                        }
                        break;
                    case 38:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menushini_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menushini_staticblockid'));
                        }
                        break;
                    case 7:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menuactii_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menuactii_staticblockid'));
                        }
                        break;
                    default:
                        if (Mage::getStoreConfig('sitemaster_categorybanner/general/menumasla_staticblockid') != '-1') {
                            $category->setDisplayMode('PRODUCTS_AND_PAGE');
                            $category->setlanding_page(Mage::getStoreConfig('sitemaster_categorybanner/general/menumasla_staticblockid'));
                        }
                }
            }
        }
    }
}