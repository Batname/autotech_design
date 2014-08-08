<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_FinderTests_UpdateTest extends VF_TestCase
{
    function testShouldUpdate()
    {
        $noteId = $this->noteFinder()->insert('code', 'message');
        $this->noteFinder()->update($noteId, 'new message');
        $note = $this->noteFinder()->findByCode('code');
        $this->assertEquals('new message', $note->message, 'should update a note');
    }


}