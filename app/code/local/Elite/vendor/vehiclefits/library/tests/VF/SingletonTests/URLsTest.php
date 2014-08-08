<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SingletonTest_URLsTest extends VF_TestCase
{
    /**
     * Should throw exception if trying to get process URL before one is set
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGetProcessURLBeforeOneIsSet()
    {
        $singleton = new VF_Singleton();
        $singleton->processUrl();
    }

    function testShouldSetProcessURL()
    {
        $singleton = new VF_Singleton();
        $singleton->setProcessURL('foo');
        $this->assertEquals('foo',$singleton->processUrl(), 'should set process URL');
    }

    /**
     * Should throw exception if trying to get base URL before one is set
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGetBaseURLBeforeOneIsSet()
    {
        $singleton = new VF_Singleton();
        $singleton->getBaseUrl();
    }

    function testShouldSetBaseUrl()
    {
        $singleton = new VF_Singleton();
        $singleton->setBaseURL('foo');
        $this->assertEquals('foo',$singleton->getBaseUrl(), 'should set base URL');
    }

    /**
     * Should throw exception if trying to get homepageSearchURL before one is set
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGethomepageSearchURLBeforeOneIsSet()
    {
        $singleton = new VF_Singleton();
        $singleton->homepageSearchURL();
    }

    function testShouldSetHomepageSearchUrl()
    {
        $singleton = new VF_Singleton();
        $singleton->setHomepageSearchURL('foo');
        $this->assertEquals('foo',$singleton->homepageSearchURL(), 'should set homepage search URL');
    }
}