<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_Schema_Generator extends VF_Schema_Generator
{
    function generator($levels)
    {
        $this->levels = $levels;
        $query = $this->createImportTable();
        return $query;
    }

    function createImportTable()
    {
        $query = 'CREATE TABLE elite_import (';
        $query .= ' `id` int(255) NOT NULL AUTO_INCREMENT, ';
        foreach ($this->levels() as $level) {
            $query .= sprintf("`%s` VARCHAR(255) NOT NULL,", str_replace(' ', '_', $level));
            $query .= sprintf("`%s_id` INTEGER(50) NOT NULL,", str_replace(' ', '_', $level));
        }
        $query .= ' `sku` varchar(255) NULL,';
        $query .= '`product_id` int(255) NULL,';
        $query .= '`universal` int(1)  NULL DEFAULT \'0\',';
        $query .= '`existing` INT( 1 ) NOT NULL,';
        $query .= '`line` INT( 255 ) NOT NULL,';
        $query .= '`mapping_id` INT( 255 ) NOT NULL,';
        $query .= '`note_message` VARCHAR( 255 ) NOT NULL,';
        $query .= '`notes` VARCHAR( 255 ) NOT NULL,';
        $query .= '`price` float NOT NULL,';
        $query .= 'PRIMARY KEY (`id`)';
        $query .= ') ENGINE = InnoDB CHARSET=utf8;';
        // it seems to run faster without an index. test this with performanceTests/VehiclesListImporterTest
        //$query .= $this->indexImportTable();
        return $query;
    }

    function indexImportTable()
    {
        $query = '';
        foreach ($this->levels() as $level) {
            //$query .= sprintf("ALTER TABLE `elite_import` ADD INDEX (%s);",$level);
            $query .= sprintf("ALTER TABLE `elite_import` ADD INDEX (%s);", $level . '_id');
        }
        return $query;
    }

    function getSchema()
    {
        return new VF_Schema();
    }
}