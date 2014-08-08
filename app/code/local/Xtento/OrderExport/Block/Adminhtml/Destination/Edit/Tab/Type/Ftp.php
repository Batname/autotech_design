<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-02-10T17:06:07+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Destination/Edit/Tab/Type/Ftp.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Destination_Edit_Tab_Type_Ftp
{
    // FTP Configuration
    public function getFields($form, $type = 'FTP')
    {
        $model = Mage::registry('destination');
        if ($type == 'FTP') {
            $fieldset = $form->addFieldset('config_fieldset', array(
                'legend' => Mage::helper('xtento_orderexport')->__('FTP Configuration'),
            ));
        } else {
            // SFTP
            $fieldset = $form->addFieldset('config_fieldset', array(
                'legend' => Mage::helper('xtento_orderexport')->__('SFTP Configuration'),
            ));
            $fieldset->addField('sftp_note', 'note', array(
                'text' => Mage::helper('xtento_orderexport')->__('<strong>Important</strong>: Only SFTPv3 servers are supported. Please make sure the server you\'re trying to connect to is a SFTPv3 server.')
            ));
        }

        $fieldset->addField('hostname', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('IP or Hostname'),
            'name' => 'hostname',
            'note' => Mage::helper('xtento_orderexport')->__(''),
            'required' => true,
        ));
        if ($type == 'FTP') {
            $fieldset->addField('ftp_type', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Server Type'),
                'name' => 'ftp_type',
                'options' => array(
                    Xtento_OrderExport_Model_Destination_Ftp::TYPE_FTP => 'FTP',
                    Xtento_OrderExport_Model_Destination_Ftp::TYPE_FTPS => 'FTPS ("FTP SSL")',
                ),
                'note' => Mage::helper('xtento_orderexport')->__('FTPS is only available if PHP has been compiled with OpenSSL support. Only some server versions are supported, support is limited by PHP.')
            ));
        }
        $fieldset->addField('port', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Port'),
            'name' => 'port',
            'note' => Mage::helper('xtento_orderexport')->__('Default Port: %d', ($type == 'FTP') ? 21 : 22),
            'class' => 'validate-number',
            'required' => true,
        ));
        $fieldset->addField('username', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Username'),
            'name' => 'username',
            'note' => Mage::helper('xtento_orderexport')->__(''),
            'required' => true,
        ));
        $fieldset->addField('new_password', 'obscure', array(
            'label' => Mage::helper('xtento_orderexport')->__('Password'),
            'name' => 'new_password',
            'note' => Mage::helper('xtento_orderexport')->__(''),
            'required' => true,
        ));
        $model->setNewPassword(($model->getPassword()) ? '******' : '');
        $fieldset->addField('timeout', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Timeout'),
            'name' => 'timeout',
            'note' => Mage::helper('xtento_orderexport')->__('Timeout in seconds after which the connection to the server fails'),
            'required' => true,
            'class' => 'validate-number'
        ));
        if ($type == 'FTP') {
            $fieldset->addField('ftp_pasv', 'select', array(
                'label' => Mage::helper('xtento_orderexport')->__('Enable Passive Mode'),
                'name' => 'ftp_pasv',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_orderexport')->__('If your server is behind a firewall, or if the extension has problems uploading the exported files, please set this to "Yes".')
            ));
        }
        $fieldset->addField('path', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Export Directory'),
            'name' => 'path',
            'note' => Mage::helper('xtento_orderexport')->__('This is the absolute path to the directory on the server where files will be uploaded to. This directory has to exist on the FTP server.'),
            'required' => true,
        ));
    }
}