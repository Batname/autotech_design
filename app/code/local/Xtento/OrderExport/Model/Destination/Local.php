<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-05-22T13:58:51+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Destination/Local.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Destination_Local extends Xtento_OrderExport_Model_Destination_Abstract
{
    public function testConnection()
    {
        $exportDirectory = $this->_fixBasePath($this->getDestination()->getPath());
        $testResult = new Varien_Object();

        // Check for forbidden folders
        $forbiddenFolders = array(Mage::getBaseDir('base'), Mage::getBaseDir('base') . DS . 'downloader');
        foreach ($forbiddenFolders as $forbiddenFolder) {
            if (@realpath($exportDirectory) == $forbiddenFolder) {
                return $testResult->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('It is not allowed to save export files in the directory you have specified. Please change the local export directory to be located in the ./var/ folder for example. Do not use the Magento root directory for example.'));
            }
        }

        if (!is_dir($exportDirectory) && !preg_match('/%exportid%/', $exportDirectory)) {
            // Try to create the directory.
            if (!@mkdir($exportDirectory)) {
                return $testResult->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('The specified local directory does not exist. We could not create it either. Please make sure the parent directory is writable or create the directory manually: %s', $exportDirectory));
            } else {
                $testResult->setDirectoryCreated(true);
            }
        }
        $this->_connection = @opendir($exportDirectory);
        if (!$this->_connection || @!is_writable($exportDirectory)) {
            return $testResult->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('Could not open local export directory for writing. Please make sure that we have rights to read and write in the directory: %s', $exportDirectory));
        }
        if ($testResult->getDirectoryCreated()) {
            $testResult->setSuccess(true)->setMessage(Mage::helper('xtento_orderexport')->__('Local directory didn\'t exist and was created successfully. Connection tested successfully.'));
            if (!$this->getDestination()->getBackupDestination()) {
                $this->getDestination()->setLastResult($testResult->getSuccess())->setLastResultMessage($testResult->getMessage())->save();
            }
            return $testResult;
        } else {
            $testResult->setSuccess(true)->setMessage(Mage::helper('xtento_orderexport')->__('Local directory exists and is writable. Connection tested successfully.'));
            if (!$this->getDestination()->getBackupDestination()) {
                $this->getDestination()->setLastResult($testResult->getSuccess())->setLastResultMessage($testResult->getMessage())->save();
            }
            return $testResult;
        }
    }

    public function saveFiles($fileArray)
    {
        if (empty($fileArray)) {
            return array();
        }
        $savedFiles = array();
        $logEntry = Mage::registry('export_log');
        // Test connection
        $testResult = $this->testConnection();
        if (!$testResult->getSuccess()) {
            $logEntry->setResult(Xtento_OrderExport_Model_Log::RESULT_WARNING);
            $logEntry->addResultMessage(Mage::helper('xtento_orderexport')->__('Destination "%s" (ID: %s): %s', $this->getDestination()->getName(), $this->getDestination()->getId(), $testResult->getMessage()));
            if (!$this->getDestination()->getBackupDestination()) {
                $this->getDestination()->setLastResultMessage($testResult->getMessage());
            }
            return false;
        }
        // Save files
        $exportDirectory = $this->_fixBasePath($this->getDestination()->getPath());
        foreach ($fileArray as $filename => $data) {
            $originalFilename = $filename;
            if ($this->getDestination()->getBackupDestination()) {
                // Add the export_id as prefix to uniquely store files in the backup/copy folder
                $filename = $logEntry->getId() . '_' . $filename;
            }
            if (preg_match('/%exportid%/', $exportDirectory)) {
                if (Mage::registry('export_log')) {
                    $exportId = Mage::registry('export_log')->getId();
                } else {
                    $exportId = 0;
                }
                $exportDirectory = preg_replace('/%exportid%/', $exportId, $exportDirectory);
                if (!is_dir($exportDirectory)) {
                    @mkdir($exportDirectory);
                }
            }
            if (!@file_put_contents($exportDirectory . $filename, $data) && !empty($data)) {
                $logEntry->setResult(Xtento_OrderExport_Model_Log::RESULT_WARNING);
                $message = "Could not save file $filename in directory $exportDirectory";
                $logEntry->addResultMessage(Mage::helper('xtento_orderexport')->__('Destination "%s" (ID: %s): %s', $this->getDestination()->getName(), $this->getDestination()->getId(), $message));
                if (!$this->getDestination()->getBackupDestination()) {
                    $this->getDestination()->setLastResultMessage(Mage::helper('xtento_orderexport')->__($message));
                }
            } else {
                $savedFiles[] = $exportDirectory . $originalFilename;
            }
        }
        return $savedFiles;
    }

    private function _fixBasePath($originalPath)
    {
        /*
        * Let's try to fix the import directory and replace the dot with the actual Magento root directory.
        * Why? Because if the cronjob is executed using the PHP binary a different working directory (when using a dot (.) in a directory path) could be used.
        * But Magento is able to return the right base path, so let's use it instead of the dot.
        */
        $originalPath = str_replace('/', DS, $originalPath);
        if (substr($originalPath, 0, 2) == '.' . DS) {
            return Mage::getBaseDir('base') . DS . substr($originalPath, 2);
        }
        return $originalPath;
    }
}