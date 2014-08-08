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
if(file_exists('../config.php')) {
    require_once '../config.php';
} else {
    require_once '../config.default.php';
}
require_once(getenv('PHP_MAGE_PATH').'/app/code/local/Elite/Vaf/bootstrap-tests.php');

$schemaGenerator = new VF_Schema_Generator();
$schemaGenerator->dropExistingTables();
$schemaGenerator->execute(array('make','model','year'));

$schema = new VF_Schema();

$vehicle = VF_Vehicle::create( $schema, array(
    'make' => 'Honda_Unique'.uniqid(),
    'model' => 'Civic',
    'year' => '2002'
));
$vehicle->save();

$values = $vehicle->toValueArray();

$mapping = new VF_Mapping( 1, $vehicle );
$mapping->save();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
      "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <link rel="stylesheet" href="../qunit/qunit.css" type="text/css"/>
  </head>
  <body>
    <h1 id="qunit-header">VAF - MMY</h1>
    <h2 id="qunit-banner"></h2>
    <h2 id="qunit-userAgent"></h2>
    <ol id="qunit-tests">
    </ol>
    
    <?php
    include('../search.include.php');
    $search = new Elite_Vaf_Block_Search_AjaxTestSub();
    $search->toHtml();
    ?>
    
    <script type="text/javascript" src="../qunit/qunit.js"></script>
    <script type="text/javascript" src="../common.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            
            QUnit.done = function (failures, total) {
                top.testPageComplete( 'ajaxTestJs/MMY.php', failures, total );
            };
            
            module("Loading Levels");
            
            test("Selecting a MAKE should display a loading message in the MODEL select box", function() {
                click( 'make', <?=$values['make']?> );
                selectionTextEquals( jQuery(".modelSelect"), "loading" );
            });
            
            test("Clicking Make Should Load Models", function() {
                expect(1);
                stop();
                
                jQuery(".modelSelect").bind( 'vafLevelLoaded', function() {
                    jQuery(".modelSelect").unbind('vafLevelLoaded');
                    selectionTextEquals( jQuery(".modelSelect"), "Civic" );
                    start();
                });
                
                click( 'make', <?=$values['make']?> );
            });
            
            test("Selecting a MODEL should display a loading message in the YEAR select box", function() {
                expect(1);
                click( 'model', <?=$values['make']?> );
                selectionTextEquals( jQuery(".yearSelect"), "loading" );
            });
            
            test("Clicking Model Should Load Years", function() {
                expect(1);
                stop();
                
                jQuery(".yearSelect").bind( 'vafLevelLoaded', function() {
                    jQuery(".modelSelect").unbind('vafLevelLoaded');
                    jQuery(".yearSelect").unbind('vafLevelLoaded');
                    selectionTextEquals( jQuery(".yearSelect"), "2002" );
                    start();
                });
                jQuery(".modelSelect").bind( 'vafLevelLoaded', function() { 
                    click( 'model', <?=$values['model']?> );
                });
                
                click( 'make', <?=$values['make']?> ); 
            });
            
            test("Submitting form should not cause error", function() {
                jQuery().unbind("vafSubmit");
                jQuery("#vafSubmit").trigger("click");
            });
            
        });
    </script>
  </body>
</html>
