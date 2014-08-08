<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_CSV_ImportTests_MMY_FieldsTest extends VF_Import_TestCase
{
    protected $csvData;

    function doSetUp()
    {
        $this->switchSchema('make,model,year');

        $this->csvData = 'make, model, year';
    }

    function testShouldFigureOutFieldPositions()
    {
        $importer = $this->vehiclesListImporter($this->csvData);
        $this->assertEquals(array('make' => 0, 'model' => 1, 'year' => 2), $importer->getFieldPositions(), 'should figure out field positions');
    }

    function testShouldFigureOutFieldPositions2()
    {
        $importer = $this->vehiclesListImporter($this->csvData);
        $importer->getFieldPositions();
        $importer->getFieldPositions();
        $this->assertEquals(array('make' => 0, 'model' => 1, 'year' => 2), $importer->getFieldPositions(), 'should figure out field positions (repeatable)');
    }

}