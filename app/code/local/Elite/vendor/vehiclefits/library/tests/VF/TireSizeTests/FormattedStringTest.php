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
class VF_TireSizeTests_FormattedStringTest extends VF_TestCase
{
    
    function testShouldCreateFromFormattedString()
    {
        $tireSize = VF_TireSize::create('205/55-16');
        $this->assertEquals( '205/55-16', (string)$tireSize, 'should format a tire size string' );
    }
    
    /**
    * @expectedException VF_TireSize_InvalidFormatException
    */
    function testShouldThrowExceptionForMissingSectionWidth()
    {
        $tireSize = VF_TireSize::create('/55-16');
    }
    
    /**
    * @expectedException VF_TireSize_InvalidFormatException
    */
    function testShouldThrowExceptionForMissingAspectRatio()
    {
        $tireSize = VF_TireSize::create('205/-16');
    }
    
    /**
    * @expectedException VF_TireSize_InvalidFormatException
    */
    function testShouldThrowExceptionForMissingOutsideDiameter()
    {
        $tireSize = VF_TireSize::create('205/55-');
    }
    
}