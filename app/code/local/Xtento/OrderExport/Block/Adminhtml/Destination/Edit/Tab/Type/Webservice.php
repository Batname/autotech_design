<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-02-09T22:16:56+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Destination/Edit/Tab/Type/Webservice.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Destination_Edit_Tab_Type_Webservice
{
    // Webservice Configuration
    public function getFields($form)
    {
        $fieldset = $form->addFieldset('config_fieldset', array(
            'legend' => Mage::helper('xtento_orderexport')->__('Webservice Configuration'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('webservice_note', 'note', array(
            'text' => Mage::helper('xtento_orderexport')->__('<b>Instructions</b>: To export data to a webservice, please follow the following steps:<br>1) Go into the <i>app/code/local/Xtento/OrderExport/Model/Destination/</i> directory and rename the file "Webservice.php.sample" to "Webservice.php"<br>2) Enter the function name you want to call in the Webservice.php class in the field below.<br>3) Open the Webservice.php file and add a function that matches the function name you entered. This function will be called by this destination upon exporting then.<br><br><b>Example:</b> If you enter server1 in the function name field below, a method called server1($fileArray) must exist in the Webservice.php file. This way multiple webservices can be added to the Webservice class, and can be called from different export destination, separated by the function name that is called. The function you add then gets called whenever this destination is executed by an export profile.')
        ));

        $fieldset->addField('custom_function', 'text', array(
            'label' => Mage::helper('xtento_orderexport')->__('Custom Function'),
            'name' => 'custom_function',
            'note' => Mage::helper('xtento_orderexport')->__('Please make sure the function you enter exists like this in the app/code/local/Xtento/OrderExport/Model/Destination/Webservice.php file:<br>public function <i>yourFunctionName</i>($fileArray) { ... }'),
            'required' => true
        ));
    }
}