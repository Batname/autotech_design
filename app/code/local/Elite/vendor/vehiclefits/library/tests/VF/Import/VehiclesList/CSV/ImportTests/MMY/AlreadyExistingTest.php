<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_CSV_ImportTests_MMY_AlreadyExistingTest extends VF_Import_TestCase
{

    protected $csvData;

    function doSetUp()
    {
        $this->switchSchema('make,model,year');

        $this->csvData = 'make, model, year
honda, civic, 2000
honda, civic, 2001
honda, civic, 2002
acura, integra, 2000';
    }

    function testWhenImportingMultipleTimesShouldSkipDuplicates()
    {
        $this->importVehiclesList($this->csvData);
        $expected_id = $this->levelFinder()->findEntityIdByTitle('make', 'honda');

        $this->importVehiclesList($this->csvData);
        $actual_id = $this->levelFinder()->findEntityIdByTitle('make', 'honda');
        $this->assertEquals($expected_id, $actual_id, 'when importing multiple times should skip duplicates');
    }
}