<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_CSV_ImportTests_MMCYE_PartialImportTest extends VF_Import_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,chassis,year,engine');
    }

    function testImportPartial()
    {
        $csvData = 'make,model,year';
        $csvData .= "\n";
        $csvData .= 'Honda,Accord,2000';

        $importer = $this->vehiclesListImporter($csvData);
        $importer->setConfig(new Zend_Config(array('importer' => array('allowMissingFields' => true))));
        $importer->import();

        $this->assertTrue($this->vehicleExists(array('make' => 'Honda', 'model' => 'Accord', 'engine' => 'base', 'chassis' => 'base', 'year' => '2000')), 'imports "base" for missing levels');

    }
}