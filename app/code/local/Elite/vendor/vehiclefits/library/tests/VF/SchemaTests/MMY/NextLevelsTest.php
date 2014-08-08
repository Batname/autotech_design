<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SchemaTests_MMY_NextLevelsTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testNextLevelsYear()
    {
        $schema = new VF_Schema();
        $this->assertEquals(array(), $schema->getNextLevels('year'));
    }

    function testNextLevelsModel()
    {
        $schema = new VF_Schema();
        $this->assertEquals(array('year'), $schema->getNextLevels('model'));
    }

    function testNextLevelsMake()
    {
        $schema = new VF_Schema();
        $this->assertEquals(array('model', 'year'), $schema->getNextLevels('make'));
    }
}