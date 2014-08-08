<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Schema implements VF_Configurable
{
    /** @var Zend_Config */
    protected $config;

    static public $levels;

    static public $global_status;

    protected $id;

    function __construct($id = 1)
    {
        $this->id = $id;
    }

    static function create($levels)
    {
        $schema = new VF_Schema;
        $schema->getReadAdapter()->insert('elite_schema', array(
            'key' => 'levels',
            'value' => $levels
        ));
        $schema->setId($schema->getReadAdapter()->lastInsertId());

        $schemaGenerator = new VF_Schema_Generator();
        $schemaGenerator->execute(explode(',', $levels), false, $schema->id());

        return $schema;
    }

    function id()
    {
        return isset($this->id) ? $this->id : 1;
    }

    function setId($id)
    {
        $this->id = $id;
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

    function getLeafLevel()
    {
        $levels = $this->getLevels();
        $leaf = $levels[count($levels) - 1];
        return $leaf;
    }

    function getRootLevel()
    {
        $levels = $this->getLevels();
        $root = $levels[0];
        return $root;
    }

    function getLevelsString()
    {
        $levelsString = array();
        foreach ($this->getLevels() as $level) {
            $levelsString[] = '`' . $level . '`';
        }
        return implode(',', $levelsString);
    }

    function getLevels()
    {
        $levels = isset(self::$levels[$this->id()]) ? self::$levels[$this->id()] : null;

        if (is_array($levels) && count($levels)) {
            return $levels;
        }

        $select = $this->getReadAdapter()->select()
            ->from('elite_schema', 'value')
            ->where('`key`=?', 'levels')
            ->where('id=?', $this->id());
        $levels = $select->query()->fetchColumn();

        $levels = explode(',', $levels);
        foreach ($levels as $k => $level) {
            $levels[$k] = trim($level);
        }
        return self::$levels[$this->id()] = $levels;
    }

    function getRewriteLevels()
    {
        if ($this->getConfig()->seo->rewriteLevels) {
            return explode(',', $this->getConfig()->seo->rewriteLevels);
        }
        return $this->getLevels();
    }

    /**
     * Get the level that comes immediatly previous to the requested level, or false if none
     * @param string $level
     * @return mixed string level, false if none
     */
    function getPrevLevel($level)
    {
        $levels = $this->getLevels();
        $level_incdeces = $this->getLevelIndeces();
        $level_index = $level_incdeces[$level];
        if (!isset($levels[$level_index - 1])) {
            return false;
        }
        $prev_level = $levels[$level_index - 1];
        return $prev_level;
    }

    /**
     * Get levels that come previous to the requested level. Does not include the requested level in the return array
     * @param string $deltaLevel Name of level
     * @return array of level names
     */
    function getPrevLevels($deltaLevel)
    {
        $return = array();
        foreach ($this->getLevels() as $level) {
            if ($deltaLevel == $level) {
                return $return;
            }
            $return[] = $level;
        }
        return $return;
    }

    /**
     * Get levels that come previous to the requested level, including the requested level. Includes the requested level in the return array
     * @param string $deltaLevel Name of level
     * @return array of level names
     */
    function getPrevLevelsIncluding($deltaLevel)
    {
        $return = array();
        foreach ($this->getLevels() as $level) {
            $return[] = $level;
            if ($deltaLevel == $level) {
                return $return;
            }
        }
        return $return;
    }

    /**
     * Get the level that comes immediatly after to the requested level, or false if none
     * @param string $level
     * @return mixed string level, false if none
     */
    function getNextLevel($level)
    {
        $levels = $this->getLevels();
        $level_incdeces = $this->getLevelIndeces();
        $level_index = $level_incdeces[$level];
        if (!isset($levels[$level_index + 1])) {
            return false;
        }
        $next_level = $levels[$level_index + 1];
        return $next_level;
    }

    /**
     * Get levels that come after to the requested level, not including the requested level
     * @param string $deltaLevel Name of level
     * @return array of level names
     */
    function getNextLevels($deltaLevel)
    {
        $prevLevels = $this->getPrevLevels($deltaLevel);
        $return = $this->getLevels();
        foreach ($return as $key => $level) {
            if (in_array($level, $prevLevels) || $level == $deltaLevel) {
                unset($return[$key]);
            }
        }
        return array_values($return);
    }

    /**
     * Get levels that come after to the requested level, including the requested level.
     * @param string $deltaLevel Name of level
     * @return array of level names
     */
    function getNextLevelsIncluding($deltaLevel)
    {
        $prevLevels = $this->getPrevLevels($deltaLevel);
        $return = $this->getLevels();
        foreach ($return as $key => $level) {
            if (in_array($level, $prevLevels)) {
                unset($return[$key]);
            }
        }
        return array_values($return);
    }

    /**
     * Returns true only if $level comes before $compareLevel
     * @param string $level
     * @param string $compareLevel
     */
    function levelIsBefore($level, $compareLevel)
    {
        $level_incdeces = $this->getLevelIndeces();
        $level_index = $level_incdeces[$level];
        $level_index_compare = $level_incdeces[$compareLevel];
        return $level_index < $level_index_compare;
    }

    function getLevelsExceptLeaf()
    {
        return $this->getLevelsExcluding($this->getLeafLevel());
    }

    function getLevelsExceptRoot()
    {
        return $this->getLevelsExcluding($this->getRootLevel());
    }

    function getLevelsExcluding($excludeLevel)
    {
        $return = array();
        foreach ($this->getLevels() as $level) {
            if ($excludeLevel != $level) {
                array_push($return, $level);
            }
        }
        return $return;
    }

    function hasParent($level)
    {
        return $level != $this->getRootLevel();
    }

    function getSorting($level)
    {
        $key = $level . '_sorting';
        return $this->readSchemaRegistryKey($key);
    }

    function readSchemaRegistryKey($key)
    {
        $select = $this->getReadAdapter()->select()
            ->from('elite_schema', array('value'))
            ->where('`key`=?', $key)
            ->limit(1);
        $result = $this->getReadAdapter()->query($select);
        return $result->fetchColumn();
    }

    function definitionTable()
    {
        return 'elite_' . $this->id() . '_definition';
    }

    function mappingsTable()
    {
        return 'elite_' . $this->id() . '_mapping';
    }

    function levelTable($level)
    {
        return 'elite_level_' . $this->id() . '_' . str_replace(' ', '_', $level);
    }

    /** @return Zend_Db_Adapter_Abstract */
    function getReadAdapter()
    {
        return VF_Singleton::getInstance()->getReadAdapter();
    }

    static function reset()
    {
        self::$levels = '';
        self::$global_status = '';
    }

    protected function getLevelIndeces()
    {
        return array_flip($this->getLevels());
    }
}