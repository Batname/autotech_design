<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_ProductFitments_CSV_ImportTests_MMY_LongTitleTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->csvData = 'sku, make, model, year
sku, honda, 7.3L V8 Powerstroke Diesel W Dual Alternator Top Alternator, 2000';

        $this->insertProduct(self::SKU);
    }

    function testSku()
    {
        $this->mappingsImport($this->csvData);
        $fit = $this->getFitForSku('sku');
        $this->assertEquals('7.3L V8 Powerstroke Diesel W Dual Alternator Top Alternator', $fit->getLevel('model')->getTitle());
    }


}