<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/** - 0000371: [Administrator / Product Edit / Assigning Fits] Importer is inserting values into universal incorrectly - confirmed. */
class VF_Import_ProductFitments_CSV_ImportTests_MMY_NoUniversalRegressionTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->insertProduct(self::SKU);
    }

    function test1()
    {
        $this->mappingsImport(
            'year, sku, make, model' . "\n" .
                '2002,sku,honda,civic');

        $fit = $this->getFitForSku(self::SKU);
        $this->assertEquals('honda', $fit->getLevel('make')->getTitle());
    }

}
