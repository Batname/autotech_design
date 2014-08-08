<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-02-10T17:01:41+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Output/Csv.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Output_Csv extends Xtento_OrderExport_Model_Output_Abstract
{
    public function convertData($exportArray)
    {
        return array(); // Not yet implemented.
        /*
        // Convert to XML first
        $convertedData = Mage::getModel('xtento_orderexport/output_xml', array('profile' => $this->getProfile()))->convertData($exportArray);
        // Get "first" file from returned data.
        $convertedXml = array_pop($convertedData);

        $filename = $this->_replaceFilenameVariables($profile->getFilename(), $exportArray);
        $charsetEncoding = $profile->getEncoding();
        $outputXml = $this->_changeEncoding($outputXml, $charsetEncoding);
        $outputData[$filename] = $outputXml;

        // Return data
        return $outputData;
        */
    }
}