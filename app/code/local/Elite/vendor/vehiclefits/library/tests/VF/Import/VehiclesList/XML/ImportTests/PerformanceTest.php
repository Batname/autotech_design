<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_XML_ImportTests_MMY_PerformanceTest extends VF_Import_VehiclesList_XML_TestCase
{
    protected $xmlFile;

    function doSetUp()
    {
        $this->switchSchema('make,model,year');

        $this->xmlFile = $this->xmlFile();
    }

    function testPerformance()
    {
        // should import ~500 vehicles in <= 5 seconds
        $this->setMaxRunningTime(5);

        $importer = $this->vehiclesListImporter($this->xmlFile);
        $importer->import();
    }

    function xmlFile()
    {
        return dirname(__FILE__) . '/PerformanceTest.xml';
    }

}
