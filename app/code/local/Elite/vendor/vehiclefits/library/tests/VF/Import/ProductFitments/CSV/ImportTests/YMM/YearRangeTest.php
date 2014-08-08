<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_ProductFitments_CSV_ImportTests_YMM_YearRangeTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('year,make,model');

        $this->csvData = 'sku, make, model, year_start, year_end
sku, honda, civic, 2000, 2001';

        $this->insertProduct(self::SKU);
    }

    function testYear2000()
    {
        $this->mappingsImport($this->csvData);
        $this->assertTrue($this->vehicleExists(array('year' => '2000')), '');
    }

    function testYear2001()
    {
        $this->mappingsImport($this->csvData);
        $this->assertTrue($this->vehicleExists(array('year' => '2001')), '');
    }

}