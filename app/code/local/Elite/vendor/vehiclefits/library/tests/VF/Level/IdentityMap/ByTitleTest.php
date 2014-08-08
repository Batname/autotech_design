<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_IdentityMap_ByTitleTest extends VF_TestCase
{
    function testPrefixingZero()
    {
        $identityMap = new VF_Level_IdentityMap_ByTitle();
        $identityMap->add(1, 'make', '01');
        $this->assertFalse($identityMap->has('make', '1'));
    }

    function testPrefixingZero2()
    {
        $identityMap = new VF_Level_IdentityMap_ByTitle();
        $identityMap->add(1, 'make', '01');
        $this->assertFalse($identityMap->get('make', '1'));
    }
}