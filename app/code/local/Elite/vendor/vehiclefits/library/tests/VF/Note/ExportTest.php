<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_ExportTest extends VF_TestCase
{
    function testExport()
    {
        $finder = new VF_Note_Finder();
        $noteId = $finder->insert('code1', 'message1');

        $export = new VF_Note_Export;
        $csv = $export->export();

        $expected = '"id","code","message"
"' . $noteId . '","code1","message1"
';

        $this->assertEquals($expected, $csv);
    }
}