<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_SchemaTests_LevelsStringTest extends VF_TestCase
{

    function doSetUp()
    {
        $this->switchSchema('foo,bar');
    }

    function testLevelsFB()
    {
        $schema = new VF_Schema();
        $this->assertEquals('`foo`,`bar`', $schema->getLevelsString(), 'should escape levels for mysql');
    }

}
