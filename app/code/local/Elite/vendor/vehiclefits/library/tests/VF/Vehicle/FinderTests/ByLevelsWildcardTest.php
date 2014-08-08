<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_FinderTests_ByLevelsWildcardTest extends VF_Vehicle_FinderTests_TestCase
{
    function testWildcard()
    {
        $this->createMMY('Honda', 'F-150', '2000');
        $vehicles = $this->getFinder()->findByLevels(array('make' => 'Honda', 'model' => 'F-*', 'year' => 2000));
        $this->assertEquals(1, count($vehicles));
    }

}