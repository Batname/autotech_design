<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SingletonTest_ConfigTest extends VF_TestCase
{
    function testGetConfigSearch()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->search instanceof Zend_Config, 'search section should exist in default configuration');
    }

    function testGetConfigCategory()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->category instanceof Zend_Config, 'category section should exist in default configuration');
    }

    function testGetConfigDirectory()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->directory instanceof Zend_Config, 'directory section should exist in default configuration');
    }

    function testGetConfigHomepageSearch()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->homepagesearch instanceof Zend_Config, 'homepagesearch section should exist in default configuration');
    }

    function testGetConfigCategoryChooser()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->categorychooser instanceof Zend_Config, 'categorychooser section should exist in default configuration');
    }

    function testGetConfigMyGarage()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->mygarage instanceof Zend_Config, 'mygarage section should exist in default configuration');
    }

    function testGetConfigSeo()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->seo instanceof Zend_Config, 'seo section should exist in default configuration');
    }

    function testGetConfigProduct()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->product instanceof Zend_Config, 'product section should exist in default configuration');
    }

    function testGetConfigLogos()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->logo instanceof Zend_Config, 'logo section should exist in default configuration');
    }

    function testGetConfigImporter()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->importer instanceof Zend_Config, 'importer section should exist in default configuration');
    }

    function testGetConfigTire()
    {
        $config = $this->getHelper()->getConfig();
        $this->assertTrue($config->tire instanceof Zend_Config, 'tire section should exist in default configuration');
    }

}