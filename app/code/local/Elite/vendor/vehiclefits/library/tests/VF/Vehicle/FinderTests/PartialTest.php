<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_FinderTests_PartialTest extends VF_Vehicle_FinderTests_TestCase
{
    function testShouldFindByAllLevels()
    {
        $vehicleCreated = $this->createVehicle(array('make' => 'Ford/Honda'));

        $vehicleFound = $this->getFinder()->findOneByLevelIds(array('make' => $vehicleCreated->getValue('make')), VF_Vehicle_Finder::INCLUDE_PARTIALS);
        $this->assertEquals($vehicleCreated->getValue('make'), $vehicleFound->getValue('make'), 'should find newly created level');
    }
}