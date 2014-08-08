<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SchemaTests_SchemaReservedNameTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,version');
    }

    function testLevels()
    {
        $schema = new VF_Schema();
        $this->assertEquals(array('make', 'model', 'version'), $schema->getLevels(), 'should get levels');
    }
}
