<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_VehicleMultipleSchemaTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testVehicleShouldHaveRightSchema()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = VF_Vehicle::create($schema, array('foo' => 'valfoo', 'bar' => 'valbar'));
        $this->assertEquals($schema->id(), $vehicle->schema()->id(), 'vehicle should have right schema');
    }

    function testLevelsShouldHaveRightSchema()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = VF_Vehicle::create($schema, array('foo' => 'valfoo', 'bar' => 'valbar'));
        $this->assertEquals($schema->id(), $vehicle->getLevel('foo')->getSchema()->id(), 'levels should have right schema');
    }

    function testShouldSaveLevel()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = VF_Vehicle::create($schema, array('foo' => 'valfoo', 'bar' => 'valbar'));
        $vehicle->save();

        $levelFinder = new VF_Level_Finder($schema);
        $foundLevel = $levelFinder->find('foo', $vehicle->getValue('foo'));

        $this->assertEquals($vehicle->getValue('foo'), $foundLevel->getId(), 'should save & find level');
    }

    function testSaveParenetheses()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = VF_Vehicle::create($schema, array('foo' => 'valfoo', 'bar' => 'valbar'));
        $vehicle->save();

        $vehicleExists = $this->vehicleExists(array('foo' => 'valfoo', 'bar' => 'valbar'), false, $schema);
        $this->assertTrue($vehicleExists, 'should find vehicles in different schema');
    }

}