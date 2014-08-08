<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2014-04-23T15:43:48+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Destination/Email.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Destination_Email extends Xtento_OrderExport_Model_Destination_Abstract
{
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
        $this->getTestResult()->setSuccess(true)->setMessage(Mage::helper('xtento_orderexport')->__('Ready to send emails.'));
        return true;
    }

    public function saveFiles($fileArray)
    {
        if (empty($fileArray)) {
            return array();
        }
        // Init connection
        $this->initConnection();
        $savedFiles = array();

        @ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        @ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $charset = "utf-8";
        #$charset = "iso-8859-1";

        $mail = new Zend_Mail($charset);

        $setReturnPath = Mage::getStoreConfig('system/smtp/set_return_path');
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getDestination()->getEmailSender();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig('system/smtp/return_path_email');
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if ($returnPathEmail !== null) {
            $mailTransport = new Zend_Mail_Transport_Sendmail("-f" . $returnPathEmail);
            Zend_Mail::setDefaultTransport($mailTransport);
        }

        $mail->setFrom($this->getDestination()->getEmailSender(), $this->getDestination()->getEmailSender());
        foreach (explode(",", $this->getDestination()->getEmailRecipient()) as $email) {
            if ($charset === "utf-8") {
                $mail->addTo($email, '=?utf-8?B?' . base64_encode($email) . '?=');
            } else {
                $mail->addTo($email, $email);
            }
        }

        foreach ($fileArray as $filename => $data) {
            if ($this->getDestination()->getEmailAttachFiles()) {
                $attachment = $mail->createAttachment($data);
                $attachment->filename = $filename;
            }
            $savedFiles[] = $filename;
        }

        #$mail->setSubject($this->_replaceVariables($this->getDestination()->getEmailSubject(), $firstFileContent));
        if ($charset === "utf-8") {
            $mail->setSubject('=?utf-8?B?' . base64_encode($this->_replaceVariables($this->getDestination()->getEmailSubject(), implode("\n\n", $fileArray))) . '?=');
        } else {
            $mail->setSubject($this->_replaceVariables($this->getDestination()->getEmailSubject(), implode("\n\n", $fileArray)));
        }
        $mail->setBodyText($this->_replaceVariables($this->getDestination()->getEmailBody(), implode("\n\n", $fileArray)));

        try {
            if (Mage::helper('xtcore/utils')->isExtensionInstalled('Aschroder_SMTPPro') && Mage::helper('smtppro')->isEnabled()) {
                // SMTPPro extension
                $mail->send(Mage::helper('smtppro')->getTransport());
            } else {
                $mail->send();
            }
        } catch (Exception $e) {
            $this->getTestResult()->setSuccess(false)->setMessage(Mage::helper('xtento_orderexport')->__('Error while sending email: %s', $e->getMessage()));
            return false;
        }

        return $savedFiles;
    }

    private function _replaceVariables($string, $content)
    {
        $replaceableVariables = array(
            '/%d%/' => Mage::getSingleton('core/date')->date('d'),
            '/%m%/' => Mage::getSingleton('core/date')->date('m'),
            '/%y%/' => Mage::getSingleton('core/date')->date('y'),
            '/%Y%/' => Mage::getSingleton('core/date')->date('Y'),
            '/%h%/' => Mage::getSingleton('core/date')->date('H'),
            '/%i%/' => Mage::getSingleton('core/date')->date('i'),
            '/%s%/' => Mage::getSingleton('core/date')->date('s'),
            '/%exportid%/' => (Mage::registry('export_log')) ? Mage::registry('export_log')->getId() : 0,
            '/%lastexportedincrementid%/' => (Mage::registry('last_exported_increment_id')) ? Mage::registry('last_exported_increment_id') : 0,
            '/%recordcount%/' => (Mage::registry('export_log')) ? Mage::registry('export_log')->getRecordsExported() : 0,
            '/%content%/' => $content,
        );
        $string = preg_replace(array_keys($replaceableVariables), array_values($replaceableVariables), $string);
        return $string;
    }
}