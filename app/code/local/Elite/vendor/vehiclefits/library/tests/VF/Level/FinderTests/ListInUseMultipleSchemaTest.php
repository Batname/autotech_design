<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_FinderTests_ListInUseMultipleSchemaTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testShuoldListFromSecondSchema()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $schema);

        $mapping = new VF_Mapping(1, $vehicle);
        $mapping->save();

        $foo = new VF_Level('foo', null, $schema);
        $actual = $foo->listInUse();

        $this->assertEquals('123', $actual[0], 'should list for level in 2nd schema "foo"');
    }
}