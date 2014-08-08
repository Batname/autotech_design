<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_FinderTests_FindEntityIdByTitlePerformanceTest extends VF_TestCase
{
    function testRootLevel()
    {
        $originalVehicle = $this->createMMY('Honda');
        $this->startProfiling();

        $finder = new VF_Level_Finder;
        $makeId = $finder->findEntityIdByTitle('make', 'Honda');
        $makeId = $finder->findEntityIdByTitle('make', 'Honda');
        $this->assertEquals(1, $this->getQueryCount());
    }

    function testNonRootLevel()
    {
        $originalVehicle = $this->createMMY('Honda', 'Civic');
        $this->startProfiling();

        $finder = new VF_Level_Finder;
        $modelId = $finder->findEntityIdByTitle('model', 'Civic', $originalVehicle->getValue('make'));
        $modelId = $finder->findEntityIdByTitle('model', 'Civic', $originalVehicle->getValue('make'));
        $this->assertEquals(1, $this->getQueryCount());
    }

    function startProfiling()
    {
        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);
    }

    function getQueryCount()
    {
        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        return count($queries);
    }
}