<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_Note_Observer_Importer_MappingsTests_TestCase extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    function import($stringData)
    {
        $file = TEMP_PATH . '/mappings.csv';
        file_put_contents($file, $stringData);
        $importer = new VF_Import_ProductFitments_CSV_Import_TestSubClass($file);
        $importer->import();
    }
}