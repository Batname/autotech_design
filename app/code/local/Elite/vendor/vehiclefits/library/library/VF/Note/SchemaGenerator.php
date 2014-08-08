<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/** Converts a comma delimeted list of level names into a suitable DDL for the schema */
class VF_Note_SchemaGenerator
{
    function execute($showProgress = false)
    {
        $sql = $this->generator();
        foreach (explode(';', $sql) as $sql) {
            $sql = trim($sql);
            if (!empty($sql)) {
                if ($showProgress) {
                    echo '.';
                }
                $this->query($sql);
            }
        }
    }

    function generator($levels)
    {
        return 'CREATE TABLE IF NOT EXISTS `elite_note` (
		  `id` int(50) NOT NULL AUTO_INCREMENT,
		  `code` varchar(50) NOT NULL,
		  `message` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `code` (`code`)
		) ENGINE = InnoDB CHARSET=utf8;

        CREATE TABLE IF NOT EXISTS `elite_mapping_notes` (
          `fit_id` int(50) NOT NULL,
          `note_id` varchar(50) NOT NULL,
          PRIMARY KEY (`fit_id`,`note_id`)
        ) ENGINE = InnoDB CHARSET=utf8;';
    }

    protected function query($sql)
    {
        return $this->getReadAdapter()->query($sql);
    }

    /** @return Zend_Db_Adapter_Abstract */
    protected function getReadAdapter()
    {
        return VF_Singleton::getInstance()->getReadAdapter();
    }
}