<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_Finder_IdentityMapTest extends VF_TestCase
{
    function testWhenHasNoVehicles()
    {
        $this->assertFalse($this->identityMap()->has(1), 'when identity map has no vehicles, should return false for has()');
    }

    function identityMap()
    {
        return new VF_Vehicle_Finder_IdentityMap;
    }
}