<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_Vehicle_FinderTests_TestCase extends VF_TestCase
{
    protected function getFinder($schema = null)
    {
        $schema = $schema ? $schema : new VF_Schema;
        return new VF_Vehicle_Finder($schema);
    }
}
	