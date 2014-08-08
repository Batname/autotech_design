<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_ProductFitments_CSV_ExportTests_UniversalTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');

        $this->csvData = 'sku, make, model, year, universal
sku123,  ,  ,  ,1';
        $this->csvFile = TEMP_PATH . '/mappings-single.csv';
        file_put_contents($this->csvFile, $this->csvData);


        $this->insertProduct('sku123');
        $this->insertProduct('sku456');

        $importer = new VF_Import_ProductFitments_CSV_Import_TestSubClass($this->csvFile);
        $importer->import();
    }

    function testExportUniversal()
    {
        $data = $this->exportProductFitments();
        $output = explode("\n", $data);
        $this->assertEquals('sku,universal,make,model,year,notes', $output[0]);
        $this->assertEquals('sku123,1,,,,""', $output[1]);
    }

}