<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_XML_MMYTest extends VF_Import_VehiclesList_XML_TestCase
{
    protected $csvData;
    protected $csvFile;

    function doSetUp()
    {
        $this->csvData = '<?xml version="1.0" encoding="UTF-8"?>   
<vehicles>
    <definition>
        <make id="4">Honda</make>
        <model id="5">Civic</model>
        <year id="8">2000</year>
    </definition>        
</vehicles>';
        $this->csvFile = TEMP_PATH . '/definitions.xml';
        file_put_contents($this->csvFile, $this->csvData);

        $this->switchSchema('make,model,year');

        $importer = $this->vehiclesListImporter($this->csvFile);
        $importer->import();
    }

    function testImportsMakeTitle()
    {
        $exporter = new VF_Import_VehiclesList_XML_Export;

        $this->assertEquals('<?xml version="1.0"?>
<vehicles version="1.0">
    <definition>
        <make id="4">Honda</make>
        <model id="5">Civic</model>
        <year id="8">2000</year>
    </definition>
</vehicles>', $exporter->export());
    }

}
