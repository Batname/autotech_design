<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-11-09T15:50:09+01:00
 * File:          app/code/local/Xtento/OrderExport/Controller/Abstract.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    /**
     * Serve files to browser - one file can be served directly, multiple files must be served as a ZIP file.
     */
    protected function _prepareFileDownload($fileArray)
    {
        if (count($fileArray) > 1) {
            // We need to zip multiple files and return a ZIP file to browser
            if (!@class_exists('ZipArchive') && !function_exists('gzopen')) {
                $this->_getSession()->addError(Mage::helper('xtento_orderexport')->__('PHP ZIP extension not found. Please download files manually from the server, or install the ZIP extension, or export just one file with each profile.'));
                return $this->_redirectReferer();
            }
            // ZIP creation
            $zipFile = false;
            if (@class_exists('ZipArchive')) {
                // Try creating it using the PHP ZIP functions
                $zipArchive = new ZipArchive();
                $zipFile = tempnam(sys_get_temp_dir(), 'zip');
                if ($zipArchive->open($zipFile, ZIPARCHIVE::CREATE) !== TRUE) {
                    $this->_getSession()->addError(Mage::helper('xtento_orderexport')->__('Could not open file ' . $zipFile . '. ZIP creation failed.'));
                    return $this->_redirectReferer();
                }
                foreach ($fileArray as $filename => $content) {
                    $zipArchive->addFromString($filename, $content);
                }
                $zipArchive->close();
            } else if (function_exists('gzopen')) {
                // Try creating it using the PclZip class
                require_once(Mage::getModuleDir('', 'Xtento_OrderExport') . DS . 'lib' . DS . 'PclZip.php');
                $zipFile = tempnam(sys_get_temp_dir(), 'zip');
                $zipArchive = new PclZip($zipFile);
                if (!$zipArchive) {
                    $this->_getSession()->addError(Mage::helper('xtento_orderexport')->__('Could not open file ' . $zipFile . '. ZIP creation failed.'));
                    return $this->_redirectReferer();
                }
                foreach ($fileArray as $filename => $content) {
                    $zipArchive->add(array(
                        array(
                            PCLZIP_ATT_FILE_NAME => $filename,
                            PCLZIP_ATT_FILE_CONTENT => $content
                        )
                    ));
                }
            }
            if (!$zipFile) {
                $this->_getSession()->addError(Mage::helper('xtento_orderexport')->__('ZIP file couldn\'t be created.'));
                return $this->_redirectReferer();
            }
            $this->_prepareDownloadResponse("export_" . time() . ".zip", file_get_contents($zipFile));
            @unlink($zipFile);
            return $this;
        } else {
            // Just one file, output to browser
            foreach ($fileArray as $filename => $content) {
                return $this->_prepareDownloadResponse($filename, $content);
            }
        }
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_healthCheck();
        return $this;
    }

    private function _healthCheck()
    {
        // Has the module been installed properly?
        if (!Mage::helper('xtento_orderexport')->isModuleProperlyInstalled()) {
            if ($this->getRequest()->getActionName() !== 'installation') {
                $this->getResponse()->setRedirect($this->getUrl('*/orderexport_index/installation'));
                $this->getResponse()->sendResponse();
                $this->getRequest()->setDispatched(true);
                return $this;
            } else {
                return $this;
            }
        } else {
            if ($this->getRequest()->getActionName() == 'installation') {
                $this->getResponse()->setRedirect($this->getUrl('*/orderexport_profile/index'));
                $this->getResponse()->sendResponse();
                $this->getRequest()->setDispatched(true);
                return $this;
            }
        }
        // Check module status
        if (!Mage::getBlockSingleton('xtento_orderexport/adminhtml_widget_menu')->isEnabled() || !Mage::helper('xtento_orderexport')->getModuleEnabled()) {
            if ($this->getRequest()->getActionName() !== 'disabled') {
                $this->getResponse()->setRedirect($this->getUrl('*/orderexport_index/disabled'));
                $this->getResponse()->sendResponse();
                $this->getRequest()->setDispatched(true);
                return $this;
            }
        } else {
            if ($this->getRequest()->getActionName() == 'disabled') {
                $this->getResponse()->setRedirect($this->getUrl('*/orderexport_profile/index'));
                $this->getResponse()->sendResponse();
                $this->getRequest()->setDispatched(true);
                return $this;
            }
        }
        if ($this->getRequest()->getActionName() !== 'redirect') {
            // Check XSL status
            if (@!class_exists('XSLTProcessor')) {
                $this->addWarning(Mage::helper('xtento_orderexport')->__('The XSLTProcessor class could not be found. This means your PHP installation is missing XSL features. You cannot export output formats using XSL Templates without the PHP XSL extension. Please get in touch with your hoster or server administrator to add XSL to your PHP configuration.'));

            }
            // Check if this module was made for the edition (CE/PE/EE) it's being run in
            if (Xtento_OrderExport_Helper_Data::EDITION !== 'CE' && Xtento_OrderExport_Helper_Data::EDITION !== '') {
                if (Mage::helper('xtcore/utils')->getIsPEorEE() && Mage::helper('xtento_orderexport')->getModuleEnabled()) {
                    if (Xtento_OrderExport_Helper_Data::EDITION !== 'EE') {
                        $this->addError(Mage::helper('xtento_orderexport')->__('Attention: The installed extension version is not compatible with the Enterprise Edition of Magento. The compatibility of the currently installed extension version has only been confirmed with the Community Edition of Magento. Please go to <a href="https://www.xtento.com" target="_blank">www.xtento.com</a> to purchase or download the Enterprise Edition of this extension in our store if you\'ve already purchased it.'));
                    }
                }
            }
            // Check cronjob status
            if (!Mage::getStoreConfig('orderexport/general/disable_cron_warning')) {
                $profileCollection = Mage::getModel('xtento_orderexport/profile')->getCollection();
                $profileCollection->addFieldToFilter('enabled', 1); // Profile enabled
                $profileCollection->addFieldToFilter('cronjob_enabled', 1); // Cronjob enabled
                if ($profileCollection->count() > 0) {
                    if (!Mage::helper('xtcore/utils')->isCronRunning()) {
                        if ((time() - Mage::helper('xtcore/data')->getInstallationDate()) > (60 * 30)) { // Module was not installed within the last 30 minutes
                            if (Mage::helper('xtcore/utils')->getLastCronExecution() == '') {
                                $this->addWarning(Mage::helper('xtento_orderexport')->__('Cronjob status: Cron.php doesn\'t seem to be set up at all. Cron did not execute within the last 15 minutes. Please make sure to set up the cronjob as explained <a href="http://support.xtento.com/wiki/Setting_up_the_Magento_cronjob" target="_blank">here</a> and check the cron status 15 minutes after setting up the cronjob properly again.'));
                            } else {
                                $this->addWarning(Mage::helper('xtento_orderexport')->__('Cronjob status: Cron.php doesn\'t seem to be set up properly. Cron did not execute within the last 15 minutes. Please make sure to set up the cronjob as explained <a href="http://support.xtento.com/wiki/Setting_up_the_Magento_cronjob" target="_blank">here</a> and check the cron status 15 minutes after setting up the cronjob properly again.'));
                            }
                        } else {
                            // Cron status wasn't checked yet. Please check back in 30 minutes.
                        }
                    }
                }
            }
        }
    }

    private function addWarning($messageText)
    {
        return $this->_addMsg('warning', $messageText);
    }

    private function addError($messageText)
    {
        return $this->_addMsg('error', $messageText);
    }

    private function _addMsg($type, $messageText)
    {
        $messages = Mage::getSingleton('adminhtml/session')->getMessages();
        foreach ($messages->getItems() as $message) {
            if ($message->getText() == $messageText) {
                return false;
            }
        }
        return ($type === 'error') ? Mage::getSingleton('adminhtml/session')->addError($messageText) : Mage::getSingleton('adminhtml/session')->addWarning($messageText);
    }

    /* Compatibility with Magento 1.3 */
    protected function _title($text = null, $resetIfExists = true)
    {
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
            return parent::_title($text, $resetIfExists);
        }
        return $this;
    }
}
