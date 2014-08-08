<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_CSV_ImportTests_MMY_CommaTest extends VF_Import_TestCase
{
    protected $product_id;

    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testComma()
    {
        $importer = $this->vehiclesListImporter('"make", "model", "year"
"honda", "accord,civic", "2000,2003"');
        $importer->import();

        $vehicleFinder = new VF_Vehicle_Finder(new VF_Schema);
        $vehicles = $vehicleFinder->findByLevels(array('make' => 'honda'));

        $this->assertEquals(4, count($vehicles), 'should enumerate out options');
        $this->assertEquals('honda accord 2000', $vehicles[0]->__toString());
        $this->assertEquals('honda accord 2003', $vehicles[1]->__toString());
        $this->assertEquals('honda civic 2000', $vehicles[2]->__toString());
        $this->assertEquals('honda civic 2003', $vehicles[3]->__toString());
    }

    function testCommaWithSpaces()
    {
        $importer = $this->vehiclesListImporter('"make", "model", "year"
"honda", "accord, civic", "2000,2003"');
        $importer->import();

        $vehicleFinder = new VF_Vehicle_Finder(new VF_Schema);
        $this->assertTrue($this->vehicleExists(array('make' => 'honda', 'model' => 'civic', 'year' => '2000')));
    }

    function testShouldEscapeComma()
    {
        $importer = $this->vehiclesListImporter('"make", "model", "year"
"honda", "accord,, test, civic", "2000,2003"');
        $importer->import();

        $vehicleFinder = new VF_Vehicle_Finder(new VF_Schema);
        $this->assertTrue($this->vehicleExists(array('make' => 'honda', 'model' => 'accord, test', 'year' => '2000')));
    }


}