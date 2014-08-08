<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_Vehicle_FinderTests_Partial_ByLevelIdsTest extends VF_Vehicle_FinderTests_TestCase
{
    function testShouldFindVehicleWithJustTheMake()
    {
        // create a 'full' vehicle
        $vehicle = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));

        // search for a 'partial' vehicle
        $actualValues = $this->getFinder()->findOneByLevelIds(array(
            'make' => $vehicle->getValue('make')
        ), VF_Vehicle_Finder::INCLUDE_PARTIALS)
            ->toValueArray();

        $expectedValues = array(
            'make' => $vehicle->getValue('make'),
            'model' => 0,
            'year' => 0
        );
        $this->assertEquals($expectedValues, $actualValues, 'should return vehicle with just the "make" part');
    }

    function testShouldReturnOnlyPartialVehicleEvenIfThereAreFullVehicles()
    {
        // create a 'full' vehicle
        $vehicle = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));

        // search for a 'partial' vehicle
        $actualVehicles = $this->getFinder()->findByLevelIds(array(
            'make' => $vehicle->getValue('make')
        ), VF_Vehicle_Finder::INCLUDE_PARTIALS);

        $this->assertEquals(1, count($actualVehicles), 'should return only a partial vehicle, even if there are full vehicles');
    }

    function testShouldFindPartiallyCreatedVehicles()
    {
        // create a 'partial' vehicle
        $vehicle = $this->createVehicle(array(
            'make' => 'Honda'
        ));

        // search for a 'partial' vehicle
        $actualValues = $this->getFinder()->findOneByLevelIds(array(
            'make' => $vehicle->getValue('make')
        ), VF_Vehicle_Finder::INCLUDE_PARTIALS)
            ->toValueArray();

        $expectedValues = array(
            'make' => $vehicle->getValue('make'),
            'model' => 0,
            'year' => 0
        );
        $this->assertEquals($expectedValues, $actualValues, 'should find partially created vehicles');
    }

    function testShouldFindPartialVehicleMake()
    {
        $vehicle = $this->createVehicle(array(
            'make' => 'Honda'
        ));

        $vehicles = $this->getFinder()->findByLevelIds(array(
            'make' => $vehicle->getValue('make')
        ), VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals(1, count($vehicles), 'should find partial vehicle by make');
    }

    function testShouldFindPartialVehicleMake2()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $make = $vehicle->getLevel('make');

        $params = array('make' => $make->getId());
        $vehicles = $this->getFinder()->findByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals(1, count($vehicles), 'should find one vehicle');
    }

    function testPartialVehicleShouldHaveMakeID()
    {
        $vehicle = $this->createVehicle(array('make' => 'Honda'));

        $params = array('make' => $vehicle->getValue('make'));
        $vehicles = $this->getFinder()->findByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals($vehicle->getValue('make'), $vehicles[0]->getValue('make'), 'partial vehicle should have make ID');
    }

    function testZeroShouldMatchPartialVehicle()
    {
        $vehicle = $this->createVehicle(array(
            'make' => 'Honda'
        ));
        $make = $vehicle->getLevel('make');

        $params = array(
            'make' => $make->getId(),
            'model' => 0,
            'year' => 0
        );
        $vehicles = $this->getFinder()->findByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals(1, count($vehicles), 'zero should match partial vehicle');
        $this->assertEquals(0, $vehicles[0]->getValue('model'), 'zero should match partial vehicle');
        $this->assertEquals(0, $vehicles[0]->getValue('year'), 'zero should match partial vehicle');
    }

    function testNullShouldMatchPartialVehicle()
    {
        $vehicle = $this->createVehicle(array(
            'make' => 'Honda'
        ));
        $make = $vehicle->getLevel('make');

        $params = array(
            'make' => $make->getId(),
            'model' => null,
            'year' => null
        );
        $vehicles = $this->getFinder()->findByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals(1, count($vehicles), 'zero should match partial vehicle');
        $this->assertEquals(0, $vehicles[0]->getValue('model'), 'zero should match partial vehicle');
        $this->assertEquals(0, $vehicles[0]->getValue('year'), 'zero should match partial vehicle');
    }

    function testEmptyStringShouldMatchPartialVehicle()
    {
        $vehicle = $this->createVehicle(array(
            'make' => 'Honda'
        ));
        $make = $vehicle->getLevel('make');

        $params = array(
            'make' => $make->getId(),
            'model' => "",
            'year' => ""
        );
        $vehicles = $this->getFinder()->findByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals(1, count($vehicles), 'zero should match partial vehicle');
        $this->assertNotEquals(0, $vehicles[0]->getValue('make'), 'zero should match partial vehicle');
        $this->assertEquals(0, $vehicles[0]->getValue('model'), 'zero should match partial vehicle');
        $this->assertEquals(0, $vehicles[0]->getValue('year'), 'zero should match partial vehicle');
    }

    function testPartialVehicleShouldRenderToString()
    {
        $vehicle = $this->createVehicle(array(
            'make' => 'Honda'
        ));
        $make = $vehicle->getLevel('make');

        $params = array(
            'make' => $make->getId(),
            'model' => "",
            'year' => ""
        );
        $vehicles = $this->getFinder()->findByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals('Honda', $vehicles[0]->__toString(), 'partial vehicle should render to string');
    }

    function testZeroShouldExcludeFullVehicle()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');

        $params = array('make' => $vehicle->getValue('make'), 'model' => 0, 'year' => 0);
        $vehicles = $this->getFinder()->findByLevelIds($params);
        $this->assertEquals(1, count($vehicles), 'zero should exclude full vehicles');
    }

    function testShouldExcludeFullVehicle()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');

        $params = array('make' => $vehicle->getValue('make'));
        $vehicles = $this->getFinder()->findByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals(1, count($vehicles), 'zero should exclude full vehicles');
    }

    function testShouldFindPartial()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');

        $params = array('make' => $vehicle->getValue('make'));
        $vehicle = $this->getFinder()->findOneByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals(0, $vehicle->getValue('model'));
    }
}