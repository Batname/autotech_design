<?php

/**
 * Product:       Xtento_XtCore (1.0.0)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-10-30T18:37:16+01:00
 * File:          app/code/local/Xtento/XtCore/Model/Mysql4/Config.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_XtCore_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    static $_configTablesCreated = null;

    protected function _construct()
    {
        $this->_init('xtcore/config', 'config_id');
    }

    /**
     * Save config value
     */
    public function saveConfig($path, $value)
    {
        if (!$this->_getConfigTablesCreated()) {
            if (!$this->_createConfigTables()) {
                return $this;
            }
        }

        $writeAdapter = $this->_getWriteAdapter();
        $select = $writeAdapter->select()
            ->from($this->getMainTable())
            ->where('path=?', $path);
        $row = $writeAdapter->fetchRow($select);

        $newData = array(
            'path' => $path,
            'value' => $value
        );

        if ($row) {
            $whereCondition = $writeAdapter->quoteInto($this->getIdFieldName() . '=?', $row[$this->getIdFieldName()]);
            $writeAdapter->update($this->getMainTable(), $newData, $whereCondition);
        } else {
            $writeAdapter->insert($this->getMainTable(), $newData);
        }
        return $this;
    }

    /**
     * Delete config value
     */
    public function deleteConfig($path)
    {
        if (!$this->_getConfigTablesCreated()) {
            if (!$this->_createConfigTables()) {
                return $this;
            }
        }

        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->delete($this->getMainTable(), array(
            $writeAdapter->quoteInto('path=?', $path)
        ));
        return $this;
    }

    /**
     * Get config value
     */
    public function getConfigValue($path)
    {
        if (!$this->_getConfigTablesCreated()) {
            if (!$this->_createConfigTables()) {
                return null;
            }
        }

        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getMainTable())
            ->where('path=?', $path);
        $row = $readAdapter->fetchRow($select);

        if ($row) {
            return $row['value'];
        } else {
            return null;
        }
    }

    private function _getConfigTablesCreated()
    {
        // Check if DB table(s) have been created.
        if (self::$_configTablesCreated !== null) {
            return self::$_configTablesCreated;
        } else {
            try {
                self::$_configTablesCreated = ($this->getReadConnection()->showTableStatus(Mage::getSingleton('core/resource')->getTableName('xtcore_config_data')) !== false);
            } catch (Exception $e) {
                return false;
            }
            return self::$_configTablesCreated;
        }
    }

    private function _createConfigTables()
    {
        try {
            $writeAdapter = $this->_getWriteAdapter();
            $writeAdapter->query("
            CREATE TABLE IF NOT EXISTS `" . Mage::getSingleton('core/resource')->getTableName('xtcore_config_data') . "` (
              `config_id` int(10) unsigned NOT NULL auto_increment,
              `path` varchar(255) NOT NULL default 'general',
              `value` text NOT NULL,
              PRIMARY KEY  (`config_id`),
              UNIQUE KEY `config_scope` (`path`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        } catch (Exception $e) {
            return false;
        }

        return $this;
    }
}
