<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SingletonTest_SettingsTest extends VF_TestCase
{
    function testGetDefaultSearchOptionText()
    {
        $helper = $this->getHelper(array('search' => array('defaultText' => 'foo')));
        $this->assertEquals('foo', $helper->getDefaultSearchOptionText());
    }

    function testGetDefaultSearchOptionTextPerLevel()
    {
        $helper = $this->getHelper(array('search' => array('defaultText' => '- Pick %s -')));
        $this->assertEquals('- Pick Make -', $helper->getDefaultSearchOptionText('make'));
    }

    function testGetDefaultSearchOptionTextDefault()
    {
        $helper = $this->getHelper(array('search' => array()));
        $this->assertEquals('-please select-', $helper->getDefaultSearchOptionText());
    }

    function testLabelsDefaultsTrue()
    {
        $helper = $this->getHelper(array('search' => array()));
        $this->assertTrue($helper->showLabels());
    }

    function testLabelsShouldDisable()
    {
        $helper = $this->getHelper(array('search' => array('labels' => false)));
        $this->assertFalse($helper->showLabels());
    }

    function testLabelsShouldEndable()
    {
        $helper = $this->getHelper(array('search' => array('labels' => true)));
        $this->assertTrue($helper->showLabels());
    }

    function testDefaultBrTag()
    {
        $helper = $this->getHelper(array('search' => array()));
        $this->assertTrue($helper->displayBrTag());
    }

    function testDefaultDirectory()
    {
        $helper = $this->getHelper(array('directory' => array('enable' => true)));
        $this->assertTrue($helper->enableDirectory());
    }

    function testBrTag1()
    {
        $helper = $this->getHelper(array('search' => array('insertBrTag' => true)));
        $this->assertTrue($helper->displayBrTag());
    }

    function testBrTag2()
    {
        $helper = $this->getHelper(array('search' => array('insertBrTag' => false)));
        $this->assertFalse($helper->displayBrTag());
    }

    function testLoadingTextDefault()
    {
        $helper = new VF_Singleton;
        $helper = $this->getHelper(array('search' => array()));
        $this->assertEquals('loading', $helper->getLoadingText());
    }

    function testLoadingText()
    {
        $helper = new VF_Singleton;
        $helper = $this->getHelper(array('search' => array('loadingText' => 'test')));
        $this->assertEquals('test', $helper->getLoadingText());
    }

    function testLoadingTextBlank()
    {
        $helper = new VF_Singleton;
        $helper = $this->getHelper(array('search' => array('loadingText' => '')));
        $this->assertEquals('', $helper->getLoadingText());
    }

}