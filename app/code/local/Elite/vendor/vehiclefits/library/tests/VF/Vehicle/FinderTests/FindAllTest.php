<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_FinderTests_FindAllTest extends VF_Vehicle_FinderTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testFindOneVehicle()
    {
        $expectedVehicle = $this->createVehicle(array(
            'make'=>'Honda',
            'model'=>'Civic',
            'year'=>'2000'
        ));
        $vehicles = $this->getFinder()->findAll();
        $this->assertEquals($expectedVehicle->__toString(), $vehicles[0]->__toString(), 'should list the one vehicle');
    }

    function testShouldFindMultipleVehicles()
    {
        $this->createVehicles(5);
        $vehicles = $this->getFinder()->findAll();
        $this->assertEquals(5, count($vehicles), 'should find 5 vehicles');
    }

    function testShouldLimitVehicles()
    {
        $this->createVehicles(5);
        $vehicles = $this->getFinder()->findAll(2);
        $this->assertEquals(2, count($vehicles), 'should find limit vehicles to 2');
    }

    function testShouldOffsetLimitTo2ndVehicle()
    {
        $vehicle1 = $this->createVehicle(array(
            'make'=>'Honda',
            'model'=>'Civic',
            'year'=>'2000'
        ));
        $vehicle2 = $this->createVehicle(array(
            'make'=>'Honda',
            'model'=>'Civic',
            'year'=>'2001'
        ));
        $vehicles = $this->getFinder()->findAll(1,1);
        $this->assertEquals($vehicle2->__toString(), $vehicles[0]->__toString(), 'should list the 2nd vehicle');
    }

    function testShouldOffsetLimitTo1stVehicle()
    {
        $vehicle1 = $this->createVehicle(array(
            'make'=>'Honda',
            'model'=>'Civic',
            'year'=>'2000'
        ));
        $vehicle2 = $this->createVehicle(array(
            'make'=>'Honda',
            'model'=>'Civic',
            'year'=>'2001'
        ));
        $vehicles = $this->getFinder()->findAll(1,0);
        $this->assertEquals($vehicle1->__toString(), $vehicles[0]->__toString(), 'should list the 1st vehicle');
    }

    function testShouldCountAll()
    {
        $this->createVehicles(5);
        $count = $this->getFinder()->countAll();
        $this->assertEquals(5,$count,'should count all vehicles');
    }

    function createVehicles($count)
    {
        for($i=1; $i<=$count; $i++) {
            $this->createVehicle(array(
                'make'=>'Honda',
                'model'=>'Civic',
                'year'=>uniqid()
            ));
        }
    }
}