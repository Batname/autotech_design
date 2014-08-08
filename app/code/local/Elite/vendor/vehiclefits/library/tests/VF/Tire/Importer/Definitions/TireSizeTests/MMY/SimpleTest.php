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
class VF_Tire_Importer_Definitions_TireSizeTests_MMY_SimpleTest extends VF_Tire_Importer_Definitions_TireSizeTests_MMY_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }
    
    function testNoVehicle()
    {
        $this->importVehicleTireSizes(
            '"make","model","year","tire_size"' . "\n" .
            ', , , ');
    }
    
    function testNoTireSize()
    {
        $this->importVehicleTireSizes(
            '"make","model","year","tire_size"' . "\n" .
            'honda, civic, 2000, ');
        $vehicle = $this->findVehicleByLevelsMMY('honda','civic','2000');
        $tireSizes = $vehicle->tireSize();
        $this->assertEquals( 0, count($tireSizes), 'should import no tire size' );
    }
    
    function testShouldImportSectionWidth()
    {
        $this->importVehicleTireSizes(
            '"make","model","year","tire_size"' . "\n" .
            'honda, civic, 2000, 215/60-15');
        $vehicle = $this->findVehicleByLevelsMMY('honda','civic','2000');
        $tireSizes = $vehicle->tireSize();
        $this->assertEquals( 215, $tireSizes[0]->sectionWidth(), 'should import section width' );
    }
    
    function testShouldImportAspectRatio()
    {
        $this->importVehicleTireSizes(
            '"make","model","year","tire_size"' . "\n" .
            'honda, civic, 2000, 215/60-15');
        $vehicle = $this->findVehicleByLevelsMMY('honda','civic','2000');
        $tireSizes = $vehicle->tireSize();
        $this->assertEquals( 60, $tireSizes[0]->aspectRatio(), 'should import aspect ratio' );
    }
    
    function testShouldImportDiameter()
    {
        $this->importVehicleTireSizes(
            '"make","model","year","tire_size"' . "\n" .
            'honda, civic, 2000, 215/60-15');
        $vehicle = $this->findVehicleByLevelsMMY('honda','civic','2000');
        $tireSizes = $vehicle->tireSize();
        $this->assertEquals( 15, $tireSizes[0]->diameter(), 'should import diameter' );
    }
    
}