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
if(file_exists('config.php')) {
    require_once 'config.php';
} else {
    require_once 'config.default.php';
}
require_once(getenv('PHP_MAGE_PATH').'/app/code/local/Elite/Vaf/bootstrap-tests.php');

$schemaGenerator = new VF_Schema_Generator();
$schemaGenerator->dropExistingTables();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
      "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <link rel="stylesheet" href="qunit/qunit.css" type="text/css"/>
  </head>
  <body>
    <h1 id="qunit-header">VAF - Install Test</h1>
    <h2 id="qunit-banner"></h2>
    <h2 id="qunit-userAgent"></h2>
    <ol id="qunit-tests">
    </ol>
    

    <iframe id="myframe" src="/vf-install.php" width="500" height="500"></iframe>
    
    <script type="text/javascript" src="/skin/adminhtml/default/default/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="qunit/qunit.js"></script>
    <script type="text/javascript" src="common.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            
            QUnit.done = function (failures, total) {
                top.testPageComplete( 'Installer.php', failures, total );
            };
            
            module("Installer Test");
            
            test("Should install database", function() {

                stop();
                expect(1);


                jQuery('body').on('foo',function(){
                    var text = jQuery('#myframe').contents().find('#query_text').text();
                    ok(text.search(/elite_level_1_make/) > 1);
                    start();
                });

                var button = jQuery("#myframe").contents().find('#go');
                button.click();
            });

            
        });
    </script>
  </body>
</html>
