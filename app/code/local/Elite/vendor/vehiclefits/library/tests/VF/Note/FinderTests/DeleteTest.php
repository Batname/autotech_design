<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_FinderTests_DeleteTest extends VF_TestCase
{
    function testUpdateNote()
    {
        $noteId = $this->noteFinder()->insert('code', 'message');
        $this->noteFinder()->update('code', 'new message');
        $this->noteFinder()->delete($noteId);
        $this->assertFalse($this->noteFinder()->find($noteId), 'should delete a note');
    }
}