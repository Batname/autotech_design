<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_ProductFitments_CSV_ExportTests_TestSub extends VF_Import_ProductFitments_CSV_Export
{
    function getProductTable()
    {
        return 'test_catalog_product_entity';
    }
}