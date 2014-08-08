<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_XML_Export extends VF_Import_VehiclesList_BaseExport
{
    const EOL = "\n";

    function export()
    {

        $xml = '<?xml version="1.0"?>' . self::EOL;
        $xml .= '<vehicles version="1.0">' . self::EOL;

        $rowResult = $this->rowResult();

        while ($vehicleRow = $rowResult->fetch(Zend_Db::FETCH_OBJ)) {

            $xml .= '    <definition>' . self::EOL;
            foreach ($this->schema()->getLevels() as $level) {
                $xml .= '        ' . $this->renderLevel($level, $vehicleRow);
            }
            $xml .= '    </definition>' . self::EOL;

        }
        $xml .= '</vehicles>';

        return ($xml);
    }

    function renderLevel($level, $vehicleRow)
    {
        $id = $vehicleRow->{$level . '_id'};
        $title = $vehicleRow->$level;
        return '<' . $level . ' id="' . $id . '">' . $title . '</' . $level . '>' . self::EOL;
    }
}