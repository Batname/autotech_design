<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_Vehicle_FinderTests_DistinctTest extends VF_Vehicle_FinderTests_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->createVehicle(array('make' => 'Honda', 'model' => 'Civic', 'year' => '2000'));
        $this->createVehicle(array('make' => 'Honda', 'model' => 'Civic', 'year' => '2001'));
        $this->createVehicle(array('make' => 'Ford', 'model' => 'F150', 'year' => '2000'));
        $this->createVehicle(array('make' => 'Ford', 'model' => 'F150', 'year' => '2001'));
        $this->createVehicle(array('make' => 'Ford', 'model' => 'F150', 'year' => '2002'));
    }

    function testShouldFindDistinctMakes()
    {
        $vehicles = $this->getFinder()->findDistinct(array('make'));
        $this->assertEquals(2, count($vehicles), 'should find 2 makes');
        $this->assertEquals('Ford', $vehicles[0]->__toString());
        $this->assertEquals('Honda', $vehicles[1]->__toString());
    }

    function testShouldFindCardsMadeIn2002()
    {
        $levels = array('make','model');
        $where = array('year'=>'2002');

        $vehicles = $this->getFinder()->findDistinct($levels, $where);

        $this->assertEquals(1, count($vehicles));
        $this->assertEquals('Ford F150', $vehicles[0]->__toString(), 'should find cars made in 2002');
    }

    function testShouldFindMakesAssignedToProduct()
    {
        $vehicle = $this->createVehicle(array('make' => 'Honda', 'model' => 'Civic', 'year' => '2000'));
        $this->insertMappingMMY($vehicle, 1);

        $levels = array('make');
        $where = array('product_id'=>1);

        $vehicles = $this->getFinder()->findDistinct($levels, $where);

        $this->assertEquals(1, count($vehicles));
        $this->assertEquals('Honda', $vehicles[0]->__toString(), 'should find makes assigned to product');
    }
}