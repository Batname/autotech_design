<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_FinderTests_ByLevelTest extends VF_Vehicle_FinderTests_TestCase
{
    const MAKE = 'Honda';
    const MODEL = 'Civic';
    const YEAR = '2002';

    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testFindByMake()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);
        $vehicle = $this->getFinder()->findByLevel('make', $vehicle->getLevel('make')->getId());
        $this->assertSame(self::MAKE, $vehicle->getLevel('make')->getTitle());
        $this->assertSame('', $vehicle->getLevel('model')->getTitle());
        $this->assertSame('', $vehicle->getLevel('year')->getTitle());
    }

    function testFindByModel()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);
        $vehicle = $this->getFinder()->findByLevel('model', $vehicle->getLevel('model')->getId());
        $this->assertSame(self::MAKE, $vehicle->getLevel('make')->getTitle());
        $this->assertSame(self::MODEL, $vehicle->getLevel('model')->getTitle());
        $this->assertSame('', $vehicle->getLevel('year')->getTitle());
    }

    function testFindByYear()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);
        $vehicle = $this->getFinder()->findByLevel('year', $vehicle->getLevel('year')->getId());
        $this->assertSame(self::MAKE, $vehicle->getLevel('make')->getTitle());
        $this->assertSame(self::MODEL, $vehicle->getLevel('model')->getTitle());
        $this->assertSame(self::YEAR, $vehicle->getLevel('year')->getTitle());
    }

    /**
     * @expectedException Exception
     */
    function testFindByYear2()
    {
        $vehicle1 = $this->createMMY('honda', 'civic', '2000');
        $vehicle2 = $this->createMMY('honda', 'civic2', '2000');

        $this->getFinder()->findByLevel('year', $vehicle1->getLevel('year')->getId());
    }

    function testFindById()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);
        $vehicle2 = $this->getFinder()->findById($vehicle->getId());
        $this->assertSame((int)$vehicle->getId(), (int)$vehicle2->getId());
    }

    /**
     * @expectedException VF_Exception_DefinitionNotFound
     */
    function testFindByLevelNotFoundLeaf()
    {
        $vehicle = $this->getFinder()->findByLeaf(5);
    }

    /**
     * @expectedException VF_Exception_DefinitionNotFound
     */
    function testFindByLevelNotFound()
    {
        $vehicle = $this->getFinder()->findByLevel('make', 5);
    }

}