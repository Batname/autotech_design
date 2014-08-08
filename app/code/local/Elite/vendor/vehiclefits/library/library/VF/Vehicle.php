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
class VF_Vehicle implements VF_Configurable
{
    protected $row;

    /** @var VF_Schema */
    protected $schema;

    /** @var Zend_Config */
    protected $config;

    protected $titles = array();

    protected $levels = array();

    /** @var boolean wether or not it has been initialized */
    protected $init = false;

    protected $lastFlexibleLevel;

    static function create(VF_Schema $schema, $titles = array())
    {
        $row = new stdClass();
        return new VF_Vehicle($schema, 0, $row, false, $titles);
    }

    function __construct(VF_Schema $schema, $id, $row, $lastFlexibleLevel = false, $titles = array())
    {
        $this->lastFlexibleLevel = $lastFlexibleLevel;
        $this->schema = $schema;
        $this->row = $row;
        if ($id) {
            $this->row->id = $id;
        }
        $this->titles = $titles;
    }

    function getConfig()
    {
        if (!$this->config instanceof Zend_Config) {
            $this->config = VF_Singleton::getInstance()->getConfig();
        }
        return $this->config;
    }

    function setConfig(Zend_Config $config)
    {
        $this->config = $config;
    }

    function getLevel($level)
    {
        if ($this->hasLoadedLevel($level)) {
            return $this->levels[$level];
        }
        if ($this->levelIsOutsideFlexibleSelection($level)) {
            return new VF_Level($level, 0, $this->schema());
        }
        $id = $this->getValue($level);

        $levelFinder = new VF_Level_Finder($this->schema());
        $object = $levelFinder->find($level, $id);
        if (false == $object) {
            $object = new VF_Level($level, $id, $this->schema());
            if (false == $id) {
                $title = isset($this->titles[$level]) ? $this->titles[$level] : '';
                $object->setTitle($title);
            }
        }
        $this->levels[$level] = $object;
        return $object;
    }

    function hasLoadedLevel($level)
    {
        return isset($this->levels[$level]);
    }

    function getId()
    {
        return isset($this->row->id) ? $this->row->id : 0;
    }

    function getLeafValue()
    {
        $leaf = $this->schema->getLeafLevel();
        return $this->getValue($leaf);
    }

    function __toString()
    {
        $template = $this->getConfig()->search->vehicleTemplate;
        $levels = $this->schema->getLevels();
        if ($template) {
            foreach ($levels as $level) {
                $find = '%' . $level . '%';
                $template = str_replace($find, $this->getLevel($level)->getTitle(), $template);
            }
            return trim($template);
        }

        $string = array();
        foreach ($levels as $level) {
            if ($this->levelIsOutsideFlexibleSelection($level)) {
                break;
            }
            $value = $this->getLevel($level)->getTitle();
            $string[] = $value;
        }
        return trim(implode(' ', $string));
    }

    function levelIdsTruncateAfter($level)
    {
        $ids = $this->toValueArray();
        foreach ($this->schema->getNextLevels($level) as $levelToDrop) {
            unset($ids[$levelToDrop]);
        }
        return $ids;
    }

    function toValueArray()
    {
        $array = array();

        foreach ($this->getLevelObjs() as $level) {
            if (!is_object($level)) {
                break;
            }
            $levelName = $level->getType();
            $id = $level->getId();
            $array[$levelName] = $id;
        }
        foreach ($this->getLevels() as $levelName) {
            if ($this->levelIsOutsideFlexibleSelection($levelName)) {
                $array[$levelName] = 0;
            }
        }
        return $array;
    }

    function toTitleArray($levels = array())
    {
        $array = array();

        $levels = count($levels) ? $levels : $this->getLevelObjs();
        foreach ($levels as $level) {
            if (is_string($level) && strlen($level) > 0) {
                $level = $this->getLevel($level);
            }
            if (!is_object($level)) {
                break;
            }
            $levelName = $level->getType();
            $title = $level->getTitle();
            $array[$levelName] = $title;
        }
        foreach ($this->getLevels() as $levelName) {
            if ($this->levelIsOutsideFlexibleSelection($levelName)) {
                unset($array[$levelName]);
            }
        }
        return $array;
    }

    function getLevelObjs()
    {
        $val = array();
        foreach ($this->getLevels() as $level) {
            $val[$level] = $this->getLevel($level);
        }
        return $val;
    }

    function getUrlKey()
    {
        $levels = $this->getLevelObjs();
        $array = array();
        foreach ($levels as $level) {
            array_push($array, $level);
        }
        return implode('-', $array);
    }

    function save()
    {
        $bind = array();
        foreach ($this->getLevelObjs() as $level) {
            if (!$level->getTitle()) {
                $bind[$level->getType() . '_id'] = 0;
                continue;
            }
            $level->save(null, null, false);
            $bind[str_replace(' ', '_', $level->getType()) . '_id'] = $level->getId();
            $bind[str_replace(' ', '_', $level->getType())] = $level->getTitle();
        }

        $finder = new VF_Vehicle_Finder($this->schema);
        if ($finder->vehicleExists($this->toTitleArray())) {
            $vehicle = $finder->findOneByLevels($this->toTitleArray());
            return $this->row->id = $vehicle->getId();
        }

        // doesnt exist, insert it
        $insertAdapter = new VF_Db_Adapter_InsertWrapper($this->getReadAdapter());
        $insertAdapter->insert($this->schema()->definitionTable(), $bind);
        $this->row->id = $this->getReadAdapter()->lastInsertId();
    }

    function unlink()
    {
        $where = $this->whereForUnlink();

        $result = $this->query('SELECT * FROM ' . $this->schema()->definitionTable() . ' WHERE ' . $where)->fetchAll();
        foreach ($result as $row) {
            $this->unlinkVehicle($row);
        }

    }

    function unlinkVehicle($vehicleRow)
    {
        $where = $this->whereForUnlink();
        $this->query('DELETE FROM ' . $this->schema()->definitionTable() . ' WHERE ' . $where);
        $this->query('DELETE FROM ' . $this->schema()->mappingsTable() . ' WHERE ' . $where);

        foreach (array_reverse($this->getLevelObjs()) as $level) {
            $countInUse = $this->query('SELECT count(*) from ' . $this->schema()->definitionTable() . ' WHERE ' . $level->getType() . '_id = ' . $vehicleRow[$level->getType() . '_id'])->fetchColumn();
            if (!$countInUse) {
                $levelType = $level->getType();
                $this->query('DELETE FROM ' . $this->schema()->levelTable($level->getType()) . ' WHERE id = ' . $vehicleRow[$levelType . '_id']);
                if ($this->getValue($levelType)) {
                    return;
                }
            }
        }
    }

    function whereForUnlink()
    {
        $where = array();
        foreach ($this->getLevelObjs() as $level) {
            if ($level->getId()) {
                $where[] = $this->getReadAdapter()->quoteInto($level->getType() . '_id = ?', $level->getId());
            }
        }
        $where = implode(' && ', $where);
        return $where;
    }

    protected function getLevels()
    {
        return $this->schema->getLevels();
    }

    /** @return Zend_Db_Statement_Interface */
    protected function query($sql)
    {
        return $this->getReadAdapter()->query($sql);
    }

    /** @return Zend_Db_Adapter_Abstract */
    protected function getReadAdapter()
    {
        return VF_Singleton::getInstance()->getReadAdapter();
    }

    protected function levelIsOutsideFlexibleSelection($level)
    {
        return $this->lastFlexibleLevel && $level != $this->lastFlexibleLevel && !$this->schema->levelIsBefore($level, $this->lastFlexibleLevel);
    }

    function getValue($level)
    {
        if ($this->hasLoadedLevel($level)) {
            return (int)$this->getLevel($level)->getId();
        }

        $var = str_replace(' ', '_', $level) . '_id';
        return isset($this->row->$var) ? $this->row->$var : 0;
    }

    function schema()
    {
        return $this->schema;
    }
}