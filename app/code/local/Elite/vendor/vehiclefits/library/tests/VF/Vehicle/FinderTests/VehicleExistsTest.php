<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_FinderTests_VehicleExistsTest extends VF_TestCase
{
    function testVehicleShouldExist()
    {
        $this->createMMY('Honda', 'Civic', '2000');
        $this->assertTrue($this->vehicleFinder()->vehicleExists(array('make' => 'Honda', 'model' => 'Civic', 'year' => '2000')), 'vehicle should exist');
    }

    function testVehicleShouldNotExist()
    {
        $this->assertFalse($this->vehicleFinder()->vehicleExists(array('make' => 'Honda', 'model' => 'Civic', 'year' => '2000')), 'vehicle should not exist');
    }
}