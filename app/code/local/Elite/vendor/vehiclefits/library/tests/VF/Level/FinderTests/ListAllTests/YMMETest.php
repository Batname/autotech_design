<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_FinderTests_ListAllTests_YMMETest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('year,make,model,engine');
    }

    function testYMME()
    {
        $y2000 = $this->newLevel('year', '2000');
        $y2000->save();

        $honda = $this->newLevel('make', 'Honda');
        $honda->save($y2000->getId());

        $civic = $this->newLevel('model', 'Civic');
        $civic->save($honda->getId());

        $civicEngine = $this->newLevel('engine', '1.6L');
        $civicEngine->save($civic->getId());

        $accord = $this->newLevel('model', 'Accord');
        $accord->save($honda->getId());

        $actual = $this->levelFinder()->listAll('engine', $accord->getId());
        $this->assertEquals(0, count($actual));
    }

}