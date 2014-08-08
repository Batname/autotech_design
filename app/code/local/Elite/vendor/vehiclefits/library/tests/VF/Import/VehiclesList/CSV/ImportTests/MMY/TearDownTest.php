<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_CSV_ImportTests_MMY_TearDownTest extends VF_Import_TestCase
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

    function testShouldCleanupImportTable()
    {
        $this->importVehiclesList($this->csvData);
        $count = $this->getReadAdapter()->select()->from('elite_import', 'count(*)')->query();
        $this->assertEquals(0, $count->fetchColumn(), 'should cleanup import table');
    }

    function import($stringData)
    {
        $importer = $this->vehiclesListImporter($stringData);
        $importer->import();
    }
}
