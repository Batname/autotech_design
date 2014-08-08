<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_LevelTests_PartialTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testInsertMakeCreatesPartialVehicle()
    {
        $this->createVehicle(array('make' => 'honda'));
        $vehicles = $this->vehicleFinder()->findByLevels(array('make' => 'honda'), true);
        $this->assertEquals(1, count($vehicles), 'should insert partial vehicle when inserting make');
    }

    function testInsertModelCreatesPartialVehicle()
    {
        $this->createVehicle(array('make' => 'honda', 'model' => 'civic'));
        $vehicles = $this->vehicleFinder()->findByLevels(array('make' => 'honda', 'model' => 'civic'), true);
        $this->assertEquals(1, count($vehicles), 'should insert partial vehicle when inserting make');
    }
}