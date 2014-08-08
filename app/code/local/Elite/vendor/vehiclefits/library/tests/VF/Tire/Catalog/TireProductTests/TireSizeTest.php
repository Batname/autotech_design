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
class VF_Tire_Catalog_TireProductTests_TireSizeTest extends VF_TestCase
{
    const ID = 1;
    
    function testCreateNewProduct()
    {
	$product = new VF_Product;
	$tireProduct = new VF_Tire_Catalog_TireProduct($product);
	$this->assertFalse( $tireProduct->getTireSize(), 'should create new product w/ no tire size');
    }

    function testCreateNewProduct_TireType()
    {
	$product = new VF_Product;
	$tireProduct = new VF_Tire_Catalog_TireProduct($product);
	$this->assertFalse( $tireProduct->tireType(), 'should create new product w/ no tire type');
    }

    function testWhenNoTireSize()
    {
        $product = $this->newTireProduct();
        $product->setId(1);
        $this->assertFalse( $product->getTireSize(), 'should return false when product has no tire size' );
    }
    
    function testReadBackTireSize()
    {
        $tireSize = new VF_TireSize(205, 55, 16);
        
        $product = $this->newTireProduct();
        $product->setId(1);
        $product->setTireSize($tireSize);
        
        $product = $this->newTireProduct();
        $product->setId(1);
        
        $tireSize = $product->getTireSize();
        $this->assertEquals( 205, $tireSize->sectionWidth() );
        $this->assertEquals( 55, $tireSize->aspectRatio() );
        $this->assertEquals( 16, $tireSize->diameter() );
    }
        
    function testUnsetTireSize()
    {
        $tireSize = new VF_TireSize(205, 55, 16);
        
        // set a size
        $product = $this->newTireProduct();
        $product->setId(1);
        $product->setTireSize($tireSize);
        
        // unset it
        $product = $this->newTireProduct();
        $product->setId(1);
        $product->setTireSize(false);
        
        // read it back
        $product = $this->newTireProduct();
        $product->setId(1);
        
        $this->assertFalse( $product->getTireSize() );
    }
    
    function testWhenSetTireSizeShouldBindVehicle()
    {
        $vehicle = $this->createTireMMY('Honda','Civic','2000');
        $vehicle->addTireSize( VF_TireSize::create('205/55-16') );
        
        $product = $this->newTireProduct(self::ID);
        $product->setTireSize( VF_TireSize::create('205/55-16') );
        
        $tireProduct = $this->newTireProduct(self::ID);
        $this->assertEquals( 1, count($tireProduct->getFits()), 'should add vehicles for a product via its tire size' );
    }
    
    function testDoesntInterfereWithTireSize()
    {
        $product = $this->newTireProduct(self::ID);
        $product->setTireSize( new VF_TireSize(205,55,16) );
        $product->setTireType( VF_Tire_Catalog_TireProduct::WINTER );
        $product->setTireSize( false );
        $this->assertFalse( $this->newTireProduct(self::ID)->tireType(), 'should set & unset tire type' );
    }
}