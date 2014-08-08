<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_LevelsTests_ConformTests_YMMTest extends VF_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('year,make,model');
    }

    function testConformsLevelMake()
    {
        return $this->markTestIncomplete();

        $honda = new VF_Level('make');
        $honda->setTitle('Honda');
        $honda->save();

        $honda2 = new VF_Level('make');
        $honda2->setTitle('Honda');
        $honda2->save();

        $this->assertEquals($honda->getId(), $honda2->getId(), 'when saving two makes with same title, they should get the same id');
    }
}
