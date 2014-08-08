<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_CSV_ImportTests_MMY_DuplicateTest extends VF_Import_TestCase
{

    protected $csvData;

    function doSetUp()
    {
        $this->switchSchema('make,model,year', true);

        $this->csvData = '"year","make","model","option","category","subcategory","country"
"1990","STIHL","39","Base","Chainsaw","Lawn & Garden","USA"
"1991","STIHL","39","Base","Chainsaw","Lawn & Garden","USA"
"1995","STIHL","39","Base","Chainsaw","Lawn & Garden","USA"
"1997","STIHL","39","Base","Chainsaw","Lawn & Garden","USA"';
    }

    function testShouldSkipDuplicateVehicles()
    {
        $this->createVehicle(array('make' => 'STIHL', 'model' => '39', 'year' => 1997));

        $this->importVehiclesList($this->csvData);

        $this->assertEquals('STIHL', $this->levelFinder()->findEntityByTitle('make', 'STIHL')->getTitle(), 'should skip duplicate vehicles');
        $this->assertTrue($this->vehicleExists(array('make' => 'STIHL', 'model' => '39', 'year' => '1997')), 'should skip duplicate vehicles');
    }

}