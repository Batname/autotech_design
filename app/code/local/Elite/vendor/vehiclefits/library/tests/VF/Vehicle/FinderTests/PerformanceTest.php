<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_FinderTests_PerformanceTest extends VF_TestCase
{
    function testFindById()
    {
        $vehicle = $this->createMMY();
        $finder = new VF_Vehicle_Finder(new VF_Schema());

        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);

        $finder->findById($vehicle->getId());
        $finder->findById($vehicle->getId());
        $finder->findById($vehicle->getId());
        $finder->findById($vehicle->getId());

        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        $this->assertEquals(1, count($queries));
    }

    function testFindByLevel()
    {
        $vehicle = $this->createMMY();
        $yearId = $vehicle->getValue('year');

        $finder = new VF_Vehicle_Finder(new VF_Schema());

        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);

        $finder->findByLevel('year', $yearId);
        $finder->findByLevel('year', $yearId);
        $finder->findByLevel('year', $yearId);
        $finder->findByLevel('year', $yearId);

        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        $this->assertTrue(count($queries) <= 2);
    }

    function testFindByLeaf()
    {
        $vehicle = $this->createMMY();
        $yearId = $vehicle->getValue('year');

        $finder = new VF_Vehicle_Finder(new VF_Schema());

        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);

        $finder->findByLeaf($yearId);
        $finder->findByLeaf($yearId);
        $finder->findByLeaf($yearId);
        $finder->findByLeaf($yearId);

        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        $this->assertTrue(count($queries) <= 2);
    }
}