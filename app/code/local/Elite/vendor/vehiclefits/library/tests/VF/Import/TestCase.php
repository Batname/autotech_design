<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_TestCase extends VF_TestCase
{
    function getVehiclesListExport()
    {
        return new VF_Import_VehiclesList_CSV_Export;
    }

    function importVehiclesList($csvData)
    {
        $importer = $this->vehiclesListImporter($csvData);
        $importer->import();
        return $importer;
    }

    function importVehiclesListTwice($file)
    {
        $this->importVehiclesList($file);
        return $this->importVehiclesList($file);
    }

    function vehiclesListImporter($csvData)
    {
        $file = TEMP_PATH . '/vehicles-list.csv';
        file_put_contents($file, $csvData);
        $importer = new VF_Import_VehiclesList_CSV_Import($file);
        return $importer;
    }
}