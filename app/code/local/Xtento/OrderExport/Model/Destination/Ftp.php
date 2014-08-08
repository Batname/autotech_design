<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-02-11T16:35:00+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Destination/Ftp.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Destination_Ftp extends Xtento_OrderExport_Model_Destination_Abstract
{
    const TYPE_FTP = 'ftp';
    const TYPE_FTPS = 'ftps';

    public function testConnection()
    {
        $this->initConnection();
        if (!$this->getDestination()->getBackupDestination()) {
            $this->getDestination()->setLastResult($this->getTestResult()->getSuccess())->setLastResultMessage($this->getTestResult()->getMessage())->save();
        }
        return $this->getTestResult();
    }

    public function initConnection()
    {
        $this->setDestination(Mage::getModel('xtento_orderexport/destination')->load($this->getDestination()->getId()));
        $testResult = new Varien_Object();
        $this->setTestResult($testResult);

        if ($this->getDestination()->getFtpType() == self::TYPE_FTPS) {
            if (function_exists('ftp_ssl_connect')) {
                $this->_connection = @ftp_ssl_connect($this->getDestination()->getHostname(), $this->getDestination()->getPort(), $this->getDestination()->getTimeout());
            } else {
                $this->getTestResult()->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('No FTP-SSL functions found. Please compile PHP with SSL support.'));
                return false;
            }
        } else {
            if (function_exists('ftp_connect')) {
                $this->_connection = @ftp_connect($this->getDestination()->getHostname(), $this->getDestination()->getPort(), $this->getDestination()->getTimeout());
            } else {
                $this->getTestResult()->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('No FTP functions found. Please compile PHP with FTP support.'));
                return false;
            }
        }

        if (!$this->_connection) {
            $this->getTestResult()->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('Could not connect to FTP server. Please make sure that there is no firewall blocking the outgoing connection to the FTP server and that the timeout is set to a high enough value. If this error keeps occurring, please get in touch with your server hoster / server administrator AND with the server hoster / server administrator of the remote FTP server. A firewall is probably blocking ingoing/outgoing FTP connections.'));
            return false;
        }

        if (!@ftp_login($this->_connection, $this->getDestination()->getUsername(), Mage::helper('core')->decrypt($this->getDestination()->getPassword()))) {
            $this->getTestResult()->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('Could not log into FTP server. Wrong username or password.'));
            return false;
        }

        if ($this->getDestination()->getFtpPasv()) {
            // Enable passive mode
            if (!@ftp_pasv($this->_connection, true)) {
                #$this->getTestResult()->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('Could not enable passive mode for FTP connection.'));
                #$this->getDestination()->setLastResult($this->getTestResult()->getSuccess())->setLastResultMessage($this->getTestResult()->getMessage())->save();
                #return false;
            }
        }

        if (!@ftp_chdir($this->_connection, $this->getDestination()->getPath())) {
            $this->getTestResult()->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('Could not change directory on FTP server to export directory. Please make sure the directory exists (base path must be exactly the same) and that we have rights to read in the directory.'));
            return false;
        }

        $this->getTestResult()->setSuccess(true)->setMessage(Mage::helper('xtento_orderexport')->__('Connection with FTP server tested successfully.'));
        return true;
    }


    public function saveFiles($fileArray)
    {
        if (empty($fileArray)) {
            return array();
        }
        $savedFiles = array();
        $logEntry = Mage::registry('export_log');
        // Test & init connection
        $this->initConnection();
        if (!$this->getTestResult()->getSuccess()) {
            $logEntry->setResult(Xtento_OrderExport_Model_Log::RESULT_WARNING);
            $logEntry->addResultMessage(Mage::helper('xtento_orderexport')->__('Destination "%s" (ID: %s): %s', $this->getDestination()->getName(), $this->getDestination()->getId(), $this->getTestResult()->getMessage()));
            return false;
        }

        // Save files
        foreach ($fileArray as $filename => $data) {
            $originalFilename = $filename;
            if ($this->getDestination()->getBackupDestination()) {
                // Add the export_id as prefix to uniquely store files in the backup/copy folder
                $filename = $logEntry->getId() . '_' . $filename;
            }
            $tempHandle = fopen('php://temp', 'r+');
            fwrite($tempHandle, $data);
            rewind($tempHandle);
            if (!@ftp_fput($this->_connection, $filename, $tempHandle, FTP_BINARY)) {
                $logEntry->setResult(Xtento_OrderExport_Model_Log::RESULT_WARNING);
                $message = sprintf("Could not save file %s in directory %s on FTP server %s.", $filename, $this->getDestination()->getPath(), $this->getDestination()->getHostname());
                $logEntry->addResultMessage(Mage::helper('xtento_orderexport')->__('Destination "%s" (ID: %s): %s', $this->getDestination()->getName(), $this->getDestination()->getId(), $message));
                if (!$this->getDestination()->getBackupDestination()) {
                    $this->getDestination()->setLastResultMessage(Mage::helper('xtento_orderexport')->__($message));
                }
            } else {
                $savedFiles[] = $this->getDestination()->getPath() . $originalFilename;
            }
        }
        return $savedFiles;
    }
}