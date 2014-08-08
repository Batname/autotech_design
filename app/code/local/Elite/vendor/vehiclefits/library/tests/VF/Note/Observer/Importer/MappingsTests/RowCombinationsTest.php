<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_Observer_Importer_MappingsTests_RowCombinationsTest extends VF_Note_Observer_Importer_MappingsTests_TestCase
{
    protected $product_id;

    function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->product_id = $this->insertProduct(self::SKU);
    }

    function testShouldImportNotesForYearRange()
    {
        $this->createNoteDefinition('code1', 'foo');
        $this->createNoteDefinition('code2', 'bar');

        $this->import('sku, make, model, year_start, year_end, notes' . "\n" .
            'sku, honda, civic, 2000, 2001, "code1,code2",');

        $vehicle1 = $this->vehicleFinder()->findOneByLevels(array('make' => 'honda', 'model' => 'civic', 'year' => 2000));
        $vehicle2 = $this->vehicleFinder()->findOneByLevels(array('make' => 'honda', 'model' => 'civic', 'year' => 2001));

        $product = $this->getVFProductForSku('sku');
        $noteProduct = new VF_Note_Catalog_Product($product);

        $this->assertEquals(2, $noteProduct->numberOfNotes($vehicle1));
        $this->assertEquals(2, $noteProduct->numberOfNotes($vehicle2));
    }

    function testShouldImportNotesForCommaEnumeration()
    {
        $this->createNoteDefinition('code1', 'foo');
        $this->createNoteDefinition('code2', 'bar');

        $this->import('"sku", "make", "model", "year", "notes"' . "\n" .
            '"sku", "honda", "civic,accord", "2000", "code1,code2",');

        $vehicle1 = $this->vehicleFinder()->findOneByLevels(array('make' => 'honda', 'model' => 'accord', 'year' => 2000));
        $vehicle2 = $this->vehicleFinder()->findOneByLevels(array('make' => 'honda', 'model' => 'civic', 'year' => 2000));

        $product = $this->getVFProductForSku('sku');
        $noteProduct = new VF_Note_Catalog_Product($product);

        $this->assertEquals(2, $noteProduct->numberOfNotes($vehicle1));
        $this->assertEquals(2, $noteProduct->numberOfNotes($vehicle2));
    }

    function testShouldImportNotesForAllToken()
    {
        $vehicle1 = $this->createVehicle(array('make' => 'honda', 'model' => 'accord', 'year' => 2000));
        $vehicle2 = $this->createVehicle(array('make' => 'honda', 'model' => 'civic', 'year' => 2000));

        $this->createNoteDefinition('code1', 'foo');
        $this->createNoteDefinition('code2', 'bar');

        $this->import('"sku", "make", "model", "year", "notes"' . "\n" .
            '"sku", "honda", "{{all}}", "2000", "code1,code2",');

        $product = $this->getVFProductForSku('sku');
        $noteProduct = new VF_Note_Catalog_Product($product);

        $this->assertEquals(2, $noteProduct->numberOfNotes($vehicle1));
        $this->assertEquals(2, $noteProduct->numberOfNotes($vehicle2));
    }
}