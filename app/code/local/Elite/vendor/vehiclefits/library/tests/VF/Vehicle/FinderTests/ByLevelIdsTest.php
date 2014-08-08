<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_Vehicle_FinderTests_ByLevelIdsTest extends VF_Vehicle_FinderTests_TestCase
{

    function testShouldFindByAllLevels()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $vehicles = $this->getFinder()->findByLevelIds($vehicle->toValueArray());
        $this->assertEquals(1, count($vehicles), 'should find by levels');
    }

    function testShouldFindByMake()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $vehicles = $this->getFinder()->findByLevelIds(array('make' => $vehicle->getValue('make')));
        $this->assertEquals(1, count($vehicles), 'should find by make');
    }

    function testShouldFindByMakeAlternateParamaterStyle()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $vehicles = $this->getFinder()->findByLevelIds(array('make_id' => $vehicle->getValue('make')));
        $this->assertEquals(1, count($vehicles), 'should find by make w/ alternative parameter style (make_id)');
    }

    function testShouldFindOneByLevelIds()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');

        $vehicle2 = $this->getFinder()->findOneByLevelIds(array('make_id' => $vehicle->getValue('make')));
        $this->assertEquals($vehicle->toValueArray(), $vehicle2->toValueArray(), 'should find one by level ids');
    }

    function testShouldNotFindOneByLevelIds()
    {
        $vehicle2 = $this->getFinder()->findOneByLevelIds(array('make_id' => 1));
        $this->assertFalse($vehicle2, 'should not find one by level ids');
    }

    function testShouldFindInSecondSchema()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $schema);
        $found = $this->getFinder($schema)->findOneByLevelIds(array('foo' => $vehicle->getValue('foo')));
        $this->assertEquals($vehicle->getValue('foo'), $found->getValue('foo'), 'should find in second schema');
    }

    function testShouldFindMultipleVehicles()
    {
        $vehicle1 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));
        $vehicle2 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2001'
        ));
        $vehicles = $this->getFinder()->findByLevelIds(array(
            'make' => $vehicle1->getValue('make'),
            'model' => $vehicle2->getValue('model')
        ));
        $this->assertEquals(2, count($vehicles), 'should find multiple matches');
    }

    function testShouldCountMatches()
    {
        $vehicle1 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));
        $vehicle2 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2001'
        ));
        $count = $this->getFinder()->countByLevelIds(array(
            'make' => $vehicle1->getValue('make'),
            'model' => $vehicle2->getValue('model')
        ));
        $this->assertEquals(2, $count, 'should count matches');
    }

    function testShouldNotCountNonMatches()
    {
        $vehicle1 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));
        $vehicle2 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2001'
        ));
        $count = $this->getFinder()->countByLevelIds(array(
            'make' => $vehicle1->getValue('make'),
            'model' => $vehicle2->getValue('model'),
            'year'=>$vehicle2->getValue('year')
        ));
        $this->assertEquals(1, $count, 'should not count non-matches');
    }

    function testShouldLimit()
    {
        $vehicle1 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));
        $vehicle2 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2001'
        ));
        $vehicles = $this->getFinder()->findByLevelIds(array(
            'make' => $vehicle1->getValue('make'),
            'model' => $vehicle2->getValue('model')
        ),false,1);
        $this->assertEquals(1, count($vehicles), 'should limit # of vehicles found');
    }

    function testShouldOffsetLimitAndFind1stVehicle()
    {
        $vehicle1 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));
        $vehicle2 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2001'
        ));
        $vehicles = $this->getFinder()->findByLevelIds(array(
            'make' => $vehicle1->getValue('make'),
            'model' => $vehicle2->getValue('model')
        ),false,1,0);
        $this->assertEquals('Honda Civic 2000', $vehicles[0]->__toString(), 'should offset limit & find 1st vehicle');
    }

    function testShouldOffsetLimitAndFind2ndVehicle()
    {
        $vehicle1 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));
        $vehicle2 = $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2001'
        ));
        $vehicles = $this->getFinder()->findByLevelIds(array(
            'make' => $vehicle1->getValue('make'),
            'model' => $vehicle2->getValue('model')
        ),false,1,1);
        $this->assertEquals('Honda Civic 2001', $vehicles[0]->__toString(), 'should offset limit & find 2nd vehicle');
    }

}