<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_FinderTests_ListAllTests_YMMTest extends VF_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('year,make,model');
    }

    function testFindsDistinctResults()
    {
        $vehicle1 = $this->createVehicle(array('year' => 2000, 'make' => 'Honda', 'model' => 'Civic'));
        $vehicle2 = $this->createVehicle(array('year' => 2001, 'make' => 'Honda', 'model' => 'Civic'));

        $actual = $this->levelFinder()->listAll('make', $vehicle1->getValue('year'));
        $this->assertEquals(1, count($actual), 'should find distinct makes');
    }

    function testFindsDistinctResults2()
    {
        $vehicle1 = $this->createVehicle(array('year' => 2000, 'make' => 'Honda', 'model' => 'Civic'));
        $vehicle2 = $this->createVehicle(array('year' => 2000, 'make' => 'Honda', 'model' => 'Accord'));

        $actual = $this->levelFinder()->listAll('make', $vehicle1->getValue('year'));
        $this->assertEquals(1, count($actual), 'should find distinct makes');
    }

    function testShouldFindDistinctMakesOnly()
    {
        $vehicle1 = $this->createVehicle(array('year' => 2001, 'make' => 'Honda', 'model' => 'Civic'));
        $vehicle2 = $this->createVehicle(array('year' => 2000, 'make' => 'Honda', 'model' => 'Civic'));

        $actual = $this->levelFinder()->listAll('make');
        $this->assertEquals(1, count($actual), 'should find distinct makes only');
    }
}