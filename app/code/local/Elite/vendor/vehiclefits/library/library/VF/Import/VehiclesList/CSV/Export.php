<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_Import_VehiclesList_CSV_Export extends VF_Import_VehiclesList_BaseExport
{

    function export($stream)
    {
        $this->schema = $this->schema();

        fwrite($stream, $this->cols());
        fwrite($stream, "\n");
        $this->rows($stream);
    }

    function cols()
    {
        $return = '';
        foreach ($this->schema->getLevels() as $level) {
            $insertComma = $level != $this->schema->getLeafLevel();
            $return .= $this->col($level, $insertComma);
        }
        return $return;
    }

    function col($name, $insertComma = true)
    {
        return $name . ($insertComma ? "," : "");
    }

    function rows($stream)
    {
        $rowResult = $this->rowResult();
        while ($row = $rowResult->fetch(Zend_Db::FETCH_OBJ)) {
            fwrite($stream, $this->definitionCells($row));
            fwrite($stream, "\n");
        }
    }

    function definitionCells($row)
    {
        $return = '';
        foreach ($this->schema->getLevels() as $level) {
            $insertComma = $level != $this->schema->getLeafLevel();
            $return .= $this->col($row->$level, $insertComma);
        }
        return $return;
    }

}