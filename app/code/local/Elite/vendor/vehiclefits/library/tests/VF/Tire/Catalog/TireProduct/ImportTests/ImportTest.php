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
class VF_Tire_Catalog_TireProduct_ImportTests_ImportTest extends VF_Tire_Catalog_TireProduct_ImportTests_TestCase
{    
    const SKU = 'sku';
    
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->csvData = '"sku","section_width","aspect_ratio","diameter","tire_type"
"sku","205","55","16","2"';
        $this->csvFile = TEMP_PATH . '/product-tire-sizes.csv';
        file_put_contents( $this->csvFile, $this->csvData );
        
        
        $this->insertProduct( self::SKU );
    }
    
    function testSetsSectionWidth()
    {
        $importer = $this->importer( $this->csvFile );
        $importer->import();
        $product = $this->getVFProductForSku( self::SKU );
        $product = new VF_Tire_Catalog_TireProduct($product);
        $tireSize = $product->getTireSize();
        $this->assertEquals( 205, $tireSize->sectionWidth(), 'should set section width' );
    }
    
    function testSetsAspectRatio()
    {
        $importer = $this->importer( $this->csvFile );
        $importer->import();
        $product = $this->getVFProductForSku( self::SKU );
        $product = new VF_Tire_Catalog_TireProduct($product);
        $tireSize = $product->getTireSize();
        $this->assertEquals( 55, $tireSize->aspectRatio(), 'should set aspect ratio' );
    }
        
    function testSetsDiameter()
    {
        $importer = $this->importer( $this->csvFile );
        $importer->import();
        $product = $this->getVFProductForSku( self::SKU );
        $product = new VF_Tire_Catalog_TireProduct($product);
        $tireSize = $product->getTireSize();
        $this->assertEquals( 16, $tireSize->diameter(), 'should set diameter' );
    }
            
    function testSetsTireType()
    {
        $importer = $this->importer( $this->csvFile );
        $importer->import();
        $product = $this->getVFProductForSku( self::SKU );
        $product = new VF_Tire_Catalog_TireProduct($product);
        $this->assertEquals( VF_Tire_Catalog_TireProduct::WINTER, $product->tireType(), 'should set tire type' );
    }
}