<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_AjaxTests_MultipleSchemaTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testShouldListRootLevel_WhenCalledFromFrontend()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $schema);

        $mapping = new VF_Mapping(1, $vehicle);
        $mapping->save();

        ob_start();
        $_GET['front'] = 1;
        $_GET['requestLevel'] = 'foo';
        $ajax = new VF_Ajax();
        $ajax->execute($schema);
        $actual = ob_get_clean();

        $this->assertEquals('<option value="' . $vehicle->getValue('foo') . '">123</option>', $actual, 'should list root levels from 2nd schema');
    }

    function testShouldListChildLevel_WhenCalledFromFrontend()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $schema);

        $mapping = new VF_Mapping(1, $vehicle);
        $mapping->save();

        ob_start();
        $_GET['front'] = 1;
        $_GET['requestLevel'] = 'bar';
        $_GET['foo'] = $vehicle->getValue('bar');
        $ajax = new VF_Ajax();
        $ajax->execute($schema);
        $actual = ob_get_clean();

        $this->assertEquals('<option value="' . $vehicle->getValue('bar') . '">456</option>', $actual, 'should list child levels from 2nd schema');
    }

    function testShouldListChildLevel_WhenCalledFromBackend()
    {
        $schema = VF_Schema::create('foo,bar');
        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $schema);

        $mapping = new VF_Mapping(1, $vehicle);
        $mapping->save();

        ob_start();
        $_GET['requestLevel'] = 'bar';
        $_GET['foo'] = $vehicle->getValue('bar');
        $ajax = new VF_Ajax();
        $ajax->execute($schema);
        $actual = ob_get_clean();

        $this->assertEquals('<option value="' . $vehicle->getValue('bar') . '">456</option>', $actual, 'should list child levels from 2nd schema');
    }
}