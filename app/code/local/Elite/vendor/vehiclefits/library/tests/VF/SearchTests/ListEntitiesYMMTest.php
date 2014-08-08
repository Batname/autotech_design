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
class VF_SearchTests_Search_ListEntitiesYMMTest extends VF_SearchTestCase
{
    const MODEL2 = 'model2';
    
    function doSetUp()
    {
		$this->switchSchema('year,make,model');
    }
    
    function testShouldListYearsInUse()
    {
        $vehicle = $this->createMMYWithFitment();
        $search = new VF_Search();
        $search->setRequest($this->getRequest());

        $actual = $search->listEntities( 'year', '' );
        $this->assertEquals( 1, count($actual) );
        $this->assertEquals( $vehicle->getLevel('year')->getId(), $actual[0]->getId(), 'should list years when year not yet selected' );
    }
    
    function testShouldListMakesInUse()
    {
        $vehicle = $this->createMMYWithFitment();
        $search = new VF_Search();
        $search->setRequest($this->getRequest());

        $request = $this->getRequest($vehicle->toValueArray());
        $search->setRequest($request);
        $this->setRequest($request);
        
        $actual = $search->listEntities( 'make' );
        $this->assertEquals( 1, count($actual) );
        $this->assertEquals( $vehicle->getLevel('make')->getId(), $actual[0]->getId(), 'should list makes in use when model is selected' );
    }

    function testShouldNotListMakesNotInUse()
    {
        $vehicle = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>2000));
        $search = new VF_Search();
        $search->setRequest($this->getRequest());

        $request = $this->getRequest($vehicle->toValueArray());
        $search->setRequest($request);
        $this->setRequest($request);

        $actual = $search->listEntities( 'make' );
        $this->assertEquals( 0, count($actual), 'should not list makes not in use when model is selected' );
    }

    function testShouldListYearsNotInUseIfConfigSaysTo()
    {
        $config = new Zend_Config(array('search' => array('showAllOptions' => 'true')));

        $vehicle = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>2000));

        $search = new VF_Search();
        $search->setConfig($config);
        $search->setRequest($this->getRequest());

        $request = $this->getRequest($vehicle->toValueArray());
        $search->setRequest($request);
        $this->setRequest($request);

        $actual = $search->listEntities( 'year' );
        $this->assertEquals( 1, count($actual), 'should list years not in use when config says to' );
    }

    function testShouldListMakesNotInUseIfConfigSaysTo()
    {
        $config = new Zend_Config(array('search' => array('showAllOptions' => 'true')));

        $vehicle = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>2000));

        $search = new VF_Search();
        $search->setConfig($config);
        $search->setRequest($this->getRequest());

        $request = $this->getRequest($vehicle->toValueArray());
        $search->setRequest($request);
        $this->setRequest($request);

        $actual = $search->listEntities( 'make' );
        $this->assertEquals( 1, count($actual), 'should list makes not in use when config says to' );
    }

    function testListModel()
    {
        $vehicle = $this->createMMYWithFitment();

        $search = new VF_Search();
        $search->setRequest($this->getRequest($vehicle->toValueArray()));
        $actual = $search->listEntities( 'model' );
        $this->assertEquals( 1, count($actual) );
        $this->assertEquals( $vehicle->getLevel('model')->getId(), $actual[0]->getId(), 'should list models when make is selected' );
    }

}