<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_Export
{
    function export()
    {
        $finder = new VF_Note_Finder();

        $result = '"id","code","message"';
        $result .= "\n";
        foreach ($finder->getAllNotes() as $note) {
            $result .= '"' . $note->id . '"';
            $result .= ',';
            $result .= '"' . $note->code . '"';
            $result .= ',';
            $result .= '"' . $note->message . '"';
            $result .= "\n";
        }
        return $result;
    }
}