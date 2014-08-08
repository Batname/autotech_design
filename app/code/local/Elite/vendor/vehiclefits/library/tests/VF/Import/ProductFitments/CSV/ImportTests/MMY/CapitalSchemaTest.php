<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_ProductFitments_CSV_ImportTests_MMY_CapitalSchemaTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('Make,Model,Year');
        $this->csvData = 'sku, Make, Model, Year
sku, honda, civic, 2000';

        $this->insertProduct(self::SKU);
    }

    function testSku()
    {
        $this->mappingsImport($this->csvData);
        $fit = $this->getFitForSku(self::SKU);
        $this->assertEquals('honda', $fit->getLevel('Make')->getTitle());

        $this->schemaGenerator()->dropExistingTables();
    }
}