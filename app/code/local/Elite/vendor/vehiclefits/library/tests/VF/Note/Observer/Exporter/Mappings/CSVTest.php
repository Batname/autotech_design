<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_Observer_Exporter_Mappings_CSVTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');

        $this->createNoteDefinition('code1', 'foo');
        $this->createNoteDefinition('code2', 'bar');
        $this->csvData = 'sku, make, model, year, notes
sku, honda, civic, 2000, "code1,code2"';
        $this->csvFile = TEMP_PATH . '/mappings-single.csv';
        file_put_contents($this->csvFile, $this->csvData);

        $this->insertProduct('sku');
    }

    function testNotes()
    {
        $importer = new VF_Import_ProductFitments_CSV_Import_TestSubClass($this->csvFile);
        $importer->import();
        $data = $this->exportProductFitments();
        $string = explode("\n", $data);
        $this->assertEquals("sku,universal,make,model,year,notes", $string[0]);
        $this->assertEquals("sku,0,honda,civic,2000,\"code1,code2\"", $string[1]);
    }

}

class VF_Import_ProductFitmentsExport_TestStub extends VF_Import_ProductFitments_CSV_Export
{
    function getProductTable()
    {
        return 'test_catalog_product_entity';
    }
}