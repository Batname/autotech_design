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
class VF_CLI_ImportFitments extends VF_CLI
{
    protected $opt;

    protected $options = array(
        'product-table=s'=>'Product table to use for converting IDs to SKUs',
    );

    function main()
    {
        if(!$this->lastArgument()) {
            echo 'vfmagento importvehicles <filename>'."\n";
            exit;
        }
        $file = $this->lastArgument();

        $importer = new VF_Import_ProductFitments_CSV_Import($file);
        $importer->setProductTable($this->opt->getOption('product-table'));
        $importer->import();
    }
}