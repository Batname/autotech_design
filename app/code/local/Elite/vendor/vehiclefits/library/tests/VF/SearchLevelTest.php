<?php
/**
 * Vehicle Fits
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@vehiclefits.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Vehicle Fits to newer
 * versions in the future. If you wish to customize Vehicle Fits for your
 * needs please refer to http://www.vehiclefits.com for more information.
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SearchLevelTest extends VF_TestCase
{
    const MAKE = 'Honda';
    const MODEL = 'Civic';
    const YEAR = '2002';

    function testMakeSelected()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);

        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $vehicle->getLevel('make')->getId());

        $search = new VF_Search;
        $search->setRequest($request);

        $searchlevel = new VF_SearchLevel_TestSub();
        $searchlevel->display($search, 'make');

        $entity = $this->levelFinder()->find('make', $vehicle->getValue('make'));
        $this->assertTrue($searchlevel->getSelected($entity));
    }

    // 0000468: When making an incomplete selection "change" button on my garage produces error
    function testModelSelected()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);

        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $vehicle->getLevel('make')->getId());

        $search = new VF_Search;
        $search->setRequest($request);

        $searchlevel = new VF_SearchLevel_TestSub();
        $searchlevel->display($search, 'year');

        $request->setParam('make', $vehicle->getLevel('make')->getId());
        $request->setParam('model', $vehicle->getLevel('model')->getId());

        $entity = $this->levelFinder()->find('year', $vehicle->getValue('year'));
        $this->assertFalse($searchlevel->getSelected($entity));
    }

    function testYearSelected()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);

        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $vehicle->getLevel('make')->getId());
        $request->setParam('model', $vehicle->getLevel('model')->getId());
        $request->setParam('year', $vehicle->getLevel('year')->getId());

        $search = new VF_Search;
        $search->setRequest($request);

        $searchlevel = new VF_SearchLevel_TestSub();
        $searchlevel->display($search, 'year');

        $entity = $this->levelFinder()->find('year', $vehicle->getValue('year'));
        $this->assertTrue($searchlevel->getSelected($entity));
    }

    function testYearAlnumSelected()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');

        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $vehicle->getLevel('make')->getTitle());
        $request->setParam('model', $vehicle->getLevel('model')->getTitle());
        $request->setParam('year', $vehicle->getLevel('year')->getTitle());

        $search = new VF_Search;
        $search->setRequest($request);

        $searchlevel = new VF_SearchLevel_TestSub();
        $searchlevel->display($search, 'year');


        $entity = $vehicle->getLevel('year');
        $this->assertTrue($searchlevel->getSelected($entity));
    }

}

class VF_SearchLevel_TestSub extends VF_SearchLevel
{
    function __($arg)
    {
        return $arg;
    }
}