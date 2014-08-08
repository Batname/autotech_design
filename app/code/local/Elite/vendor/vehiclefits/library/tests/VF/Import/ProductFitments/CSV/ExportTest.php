<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_ProductFitments_CSV_ExportTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');

        $this->csvData = 'sku, make, model, year, universal
sku123, honda, civic, 2001
sku456, honda, civic, 2000
sku456,acura,integra,2000
sku123,acura,integra,2004
sku123,acura,test,2002
';
        $this->csvFile = TEMP_PATH . '/mappings-single.csv';
        file_put_contents($this->csvFile, $this->csvData);

        $this->insertProduct('sku123');
        $this->insertProduct('sku456');

        $importer = new VF_Import_ProductFitments_CSV_Import($this->csvFile);
        $importer->setProductTable('test_catalog_product_entity');
        $importer->import();
    }

    function testExport()
    {
        $stream = fopen("php://temp", 'w');

        $exporter = new VF_Import_ProductFitments_CSV_Export();
        $exporter->setProductTable('test_catalog_product_entity');
        $exporter->export($stream);
        rewind($stream);

        $data = stream_get_contents($stream);

        $output = explode("\n", $data);

        $this->assertEquals('sku,universal,make,model,year,notes', $output[0]);
        $this->assertEquals('sku123,0,honda,civic,2001,""', $output[1]);
        $this->assertEquals('sku456,0,honda,civic,2000,""', $output[2]);
        $this->assertEquals('sku456,0,acura,integra,2000,""', $output[3]);
        $this->assertEquals('sku123,0,acura,integra,2004,""', $output[4]);
        $this->assertEquals('sku123,0,acura,test,2002,""', $output[5]);
    }

    function testExportUniversal()
    {
        $data = $this->exportProductFitments();
        $output = explode("\n", $data);
        $this->assertEquals('sku,universal,make,model,year,notes', $output[0]);
    }

}
