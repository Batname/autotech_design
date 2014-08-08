<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_FinderTests_FindPerformanceTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testUsesOneQuery()
    {
        $level = new VF_Level('make');
        $level->setTitle('make');
        $id = $level->save();

        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);

        $level = new VF_Level('make', $id);

        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        $this->assertEquals(1, count($queries));
    }

    function testUsesOneQueryOnMultipleCalls()
    {
        $level = new VF_Level('make');
        $level->setTitle('make');
        $id = $level->save();

        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);

        $level = new VF_Level('make', $id);
        $level = new VF_Level('make', $id);

        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        $this->assertEquals(1, count($queries));
    }

}