<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_Import_VehiclesList_XML_TestCase extends VF_TestCase
{
    function vehiclesListImporter($file)
    {
        $importer = new VF_Import_VehiclesList_XML_Import($file);
        return $importer;
    }

    function getDefinitionsData($data)
    {
        $file = TEMP_PATH . '/definitions.xml';
        file_put_contents($file, $data);

        $importer = new VF_Import_VehiclesList_XML_Import($file);
        return $importer;
    }
}
