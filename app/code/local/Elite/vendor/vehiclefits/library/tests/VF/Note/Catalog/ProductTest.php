<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Elite_Vafnote_model_Catalog_ProductTest extends VF_TestCase
{
    function testShouldFindNumberOfNotes()
    {
        $this->createNoteDefinition('code1', 'this is my message');
        $vehicle = $this->createMMY();
        $product = $this->newNoteProduct(1);
        $this->insertMappingMMY($vehicle, $product->getId());

        $product->addNote($vehicle, 'code1');
        $this->assertEquals(1, $product->numberOfNotes($vehicle), 'should find number of notes for a vehicle');
    }

    function testWhenProductDoesntFitVehicle()
    {
        $this->createNoteDefinition('code1', 'this is my message');
        $vehicle1 = $this->createMMY();
        $vehicle2 = $this->createMMY();
        $product = $this->newNoteProduct(1);
        $product->addNote($vehicle1, 'code1');
        $this->assertEquals(0, $product->numberOfNotes($vehicle2), 'should find 0 notes when product doesnt fit vehicle');
    }

}
