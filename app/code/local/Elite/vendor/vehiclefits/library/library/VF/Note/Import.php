<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_Note_Import extends VF_Import_Abstract
{

    function import()
    {
        $this->getFieldPositions();
        while ($row = $this->getReader()->getRow()) {
            $this->importRow($row);
        }
    }

    function importRow($row)
    {
        $code = $this->getFieldValue('code', $row);
        $message = $this->getFieldValue('message', $row);

        $finder = new VF_Note_Finder();
        $finder->insert($code, $message);
    }

}