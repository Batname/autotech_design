<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SearchTest extends VF_SearchTestCase
{

    function testSelected()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');

        $request = new Zend_Controller_Request_Http;
        $request->setParams($vehicle->toTitleArray());

        $search = new VF_Search;
        $search->setRequest($request);
        $this->assertEquals($vehicle->getValue('model'), $search->getSelected('model'));
    }
}