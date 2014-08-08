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
class VF_AjaxTests_MMYTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testShouldListMakes()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $this->insertMappingMMY($vehicle);
        $_GET['requestLevel'] = 'make';
        $this->assertEquals('<option value="' . $vehicle->getValue('make') . '">Honda</option>', $this->execute(), 'should list makes');
    }

    function testShouldListModels()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $this->insertMappingMMY($vehicle);
        $_GET['make'] = $vehicle->getLevel('make')->getId();
        $_GET['requestLevel'] = 'model';
        $this->assertEquals('<option value="' . $vehicle->getValue('model') . '">Civic</option>', $this->execute(), 'should list models for a make');
    }

    function testShouldListYears()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $this->insertMappingMMY($vehicle);
        $_GET['make'] = $vehicle->getLevel('make')->getId();
        $_GET['model'] = $vehicle->getLevel('model')->getId();
        $_GET['requestLevel'] = 'year';
        $this->assertEquals('<option value="' . $vehicle->getValue('year') . '">2000</option>', $this->execute(), 'should list years for a model');
    }

    function testShouldListYearsInUse()
    {
        $this->createMMY('Honda', 'Civic', '2001');
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $this->insertMappingMMY($vehicle);
        $_GET['make'] = $vehicle->getLevel('make')->getId();
        $_GET['model'] = $vehicle->getLevel('model')->getId();
        $_GET['requestLevel'] = 'year';
        $this->assertEquals('<option value="' . $vehicle->getValue('year') . '">2000</option>', $this->execute(), 'should list years for a model');
    }

    function testShouldListDistinctModelsWhenMultipleYears()
    {
        $vehicle1 = $this->createMMY('Honda', 'Civic', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);

        $_GET['make'] = $vehicle1->getLevel('make')->getId();
        $_GET['requestLevel'] = 'model';
        $this->assertEquals('<option value="' . $vehicle1->getValue('model') . '">Civic</option>', $this->execute(), 'should list models for a make');
    }

    function testShouldSortMake()
    {
        return $this->markTestIncomplete();
    }

    function testShouldSortModels()
    {
        return $this->markTestIncomplete();
    }

    function testShouldListMultipleModels()
    {
        $vehicle1 = $this->createMMY('Honda', 'Accord', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $_GET['make'] = $vehicle1->getLevel('make')->getId();
        $_GET['requestLevel'] = 'model';
        $this->assertEquals('<option value="0">-please select-</option><option value="' . $vehicle1->getValue('model') . '">Accord</option><option value="' . $vehicle2->getValue('model') . '">Civic</option>', $this->execute(), 'should list models for a make');
    }

    function testShouldNotListModelsNotInUse()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2001');
        $_GET['make'] = $vehicle->getLevel('make')->getId();
        $_GET['requestLevel'] = 'model';
        $this->assertEquals('', $this->execute(), 'should not list models not in use');
    }

    function testShouldListModelsNotInUseIfConfigSaysTo()
    {
        $vehicle = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>2000));
        $_GET['make'] = $vehicle->getValue('make');
        $_GET['requestLevel'] = 'model';

        ob_start();
        $_GET['front'] = 1;

        $config = new Zend_Config(array('search' => array('showAllOptions' => 'true')));
        $ajax = new VF_Ajax;
        $ajax->setConfig($config);
        $ajax->execute($this->getSchema());
        $actual = ob_get_clean();

        $expected = '<option value="' . $vehicle->getValue('model') . '">Civic</option>';
        $this->assertEquals($expected, $actual, 'should list models not in use if config says to');
    }

    function testShouldListMultipleModels_WithDefaultOption()
    {
        $vehicle1 = $this->createMMY('Honda', 'Accord', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $_GET['make'] = $vehicle1->getLevel('make')->getId();
        $_GET['requestLevel'] = 'model';
        $_GET['front'] = true;
        $this->assertEquals('<option value="0">-please select-</option><option value="' . $vehicle1->getValue('model') . '">Accord</option><option value="' . $vehicle2->getValue('model') . '">Civic</option>', $this->execute(), 'should list models for a make');
    }

    function testShouldListMultipleModels_WithCustomDefaultOption()
    {
        $vehicle1 = $this->createMMY('Honda', 'Accord', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $_GET['make'] = $vehicle1->getLevel('make')->getId();
        $_GET['requestLevel'] = 'model';
        $_GET['front'] = true;

        $ajax = $this->getAjax();
        $config = new Zend_Config(array('search' => array('defaultText' => '-All %s-')));
        $ajax->setConfig($config);

        ob_start();
        $ajax->execute($this->getSchema());
        $actual = ob_get_clean();
        $expected = '<option value="0">-All Model-</option><option value="' . $vehicle1->getValue('model') . '">Accord</option><option value="' . $vehicle2->getValue('model') . '">Civic</option>';

        $this->assertEquals($expected, $actual, 'should list models for a make');
    }

    function testShouldListMultipleYears()
    {
        $vehicle1 = $this->createMMY('Honda', 'Civic', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $vehicle3 = $this->createMMY('Honda', 'Civic', '2002');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $this->insertMappingMMY($vehicle3);
        $_GET['make'] = $vehicle1->getLevel('make')->getId();
        $_GET['model'] = $vehicle1->getLevel('model')->getId();
        $_GET['requestLevel'] = 'year';
        $this->assertEquals('<option value="0">-please select-</option><option value="' . $vehicle1->getValue('year') . '">2000</option><option value="' . $vehicle2->getValue('year') . '">2001</option><option value="' . $vehicle3->getValue('year') . '">2002</option>', $this->execute(), 'should list models for a make');
    }

    function execute()
    {
        ob_start();
        $_GET['front'] = 1;
        $this->getAjax()->execute($this->getSchema());
        return ob_get_clean();
    }

    /** @return VF_Ajax */
    function getAjax()
    {
        return new VF_Ajax();
    }

    /** @return VF_Schema */
    function getSchema()
    {
        return new VF_Schema();
    }
}