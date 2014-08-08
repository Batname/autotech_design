<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_Observer_Importer_MappingsTests_PreviousFitmentTest extends VF_Note_Observer_Importer_MappingsTests_TestCase
{
    protected $product_id;

    function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->product_id = $this->insertProduct('sku');
    }

    function testPreviousFitment()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $fitmentId = $this->insertMappingMMY($vehicle, $this->product_id);

        $this->createNoteDefinition('code', 'message');
        $this->import('sku,make,model,year,notes' . "\n" .
            'sku,Honda,Civic,2000,code');

        $fitId = $this->getFitIdForSku('sku');
        $notes = $this->noteFinder()->getNotes($fitId);
        $this->assertEquals(1, count($notes), 'fitment notes should supercede previous fitment');
    }
}