<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SingletonTests_RequestTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    function testReqeust()
    {
        VF_Singleton::getInstance()->getRequest()->setParams(array('make' => 'honda'));
        $this->assertEquals('honda', VF_Singleton::getInstance()->getRequest()->getParam('make'));
    }

    function testNewRequest()
    {
        $singleton = new VF_Singleton;
        $request = $singleton->getRequest(); // make sure it doesn't run Magento specific code here
        $this->assertNull($request); // we won't get here if it did.
    }

}