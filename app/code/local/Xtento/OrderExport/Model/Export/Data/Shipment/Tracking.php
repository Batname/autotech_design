<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-07-04T12:11:22+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Shipment/Tracking.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Shipment_Tracking extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Tracking information',
            'category' => 'Shipment',
            'description' => 'Export tracking information for shipments exported.',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['tracks'];
        // Fetch fields to export
        $shipment = $collectionItem->getObject();
        if (!$shipment && !$collectionItem->getId()) {
            return $returnArray;
        }
        if (!$shipment) {
            $shipment = $collectionItem;
        }

        if (!$this->fieldLoadingRequired('tracks') && !$this->fieldLoadingRequired('tracking_numbers') && !$this->fieldLoadingRequired('carriers')) {
            return $returnArray;
        }

        $tracks = $shipment->getAllTracks();

        if ($tracks) {
            $trackingNumbers = array();
            $carrierNames = array();
            foreach ($tracks as $track) {
                $this->_writeArray = & $returnArray['tracks'][];
                foreach ($track->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                    if ($key == 'number') {
                        $this->writeValue('track_number', $value);
                        $trackingNumbers[] = $value;
                    }
                    if ($key == 'track_number') {
                        $this->writeValue('number', $value);
                        $trackingNumbers[] = $value;
                    }
                    if ($key == 'title') {
                        $carrierNames[] = $value;
                    }
                }
            }
            $trackingNumbers = array_unique($trackingNumbers);
            $this->_writeArray = & $returnArray;
            $this->writeValue('tracking_numbers', implode(",", $trackingNumbers));
            $this->writeValue('carriers', implode(",", $carrierNames));
        }

        // Done
        return $returnArray;
    }
}