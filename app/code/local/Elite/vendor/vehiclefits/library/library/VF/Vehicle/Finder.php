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
class VF_Vehicle_Finder implements VF_Configurable
{

    protected $schema;
    /** @var Zend_Config */
    protected $config;

    const INCLUDE_PARTIALS = true;
    static $IDENTITY_MAP_FINDBYLEVEL = array();

    function __construct(VF_Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Find all vehicles in the system. Returns an array of VF_Vehicle objects. Optionally pass $limit & $offset
     * to return only a subset of the results, useful for pagination.
     *
     * @param null|integer $limit the limit on the number of vehicles to return
     * @param null|integer $offset the offset on the limit, useful for pagination
     * @return array of VF_Vehicle objects
     * @throws Exception Generates an exception if database communication fails
     */
    function findAll($limit=null, $offset=null)
    {
        $columnsToSelect = array('id') + $this->getColumns();

        $select = $this->getReadAdapter()->select()
            ->from('elite_' . $this->schema->id() . '_definition', $columnsToSelect);
        $this->addJoins($select, false);

        foreach ($this->schema->getLevels() as $level) {
            $select->where('elite_' . $this->schema->id() . '_definition.' . $level . '_id != 0');
        }

        if($limit) {
            $select->limit($limit, $offset);
        }

        $r = $this->query($select);
        if (!$r) {
            throw new Exception(mysql_error());
        }
        $return = array();
        while ($row = $r->fetchObject()) {
            array_push($return, new VF_Vehicle($this->schema, $row->id, $row));
        }
        return $return;
    }

    /** @return integer the total number of vehicles in the system */
    function countAll()
    {
        $select = $this->getReadAdapter()->select()
            ->from('elite_' . $this->schema->id() . '_definition', new Zend_Db_Expr('count(*)'));
        return $select->query()->fetchColumn();
    }

    function findById($id)
    {
        $identityMap = VF_Vehicle_Finder_IdentityMap::getInstance();
        if ($identityMap->has($id)) {
            return $identityMap->get($id);
        }

        $select = $this->getReadAdapter()->select()
            ->from('elite_' . $this->schema->id() . '_definition', $this->getColumns());
        $this->addJoins($select, false);
        $select->where('elite_' . $this->schema->id() . '_definition.id = ?', $id);

        $r = $this->query($select);
        if (!$r) {
            throw new Exception(mysql_error());
        }
        $row = $r->fetchObject();
        if (!is_object($row)) {
            throw new Exception('No such definition with id [' . $id . ']');
        }

        $vehicle = new VF_Vehicle($this->schema, $id, $row);
        $identityMap->add($vehicle);
        return $vehicle;
    }

    function findByLevel($level, $id)
    {
        if (!(int)$id) {
            throw new Exception('must pass an level_id, [' . $id . '] given');
        }

        if (isset(self::$IDENTITY_MAP_FINDBYLEVEL[$level . $id])) {
            return self::$IDENTITY_MAP_FINDBYLEVEL[$level . $id];
        }

        $select = $this->getReadAdapter()->select()
            ->from('elite_' . $this->schema->id() . '_definition', $this->cols($level))
            ->where(sprintf('%s_id = ?', $level), $id);

        $result = $this->query($select)->fetchAll();
        if (count($result) > 1) {
            throw new Exception('Your query is ambiguous, more than one vehicle matches this query.');
        }
        if (count($result) == 0) {
            throw new VF_Exception_DefinitionNotFound('No such definition with level [' . $level . '] and id [' . $id . ']');
        }
        $row = $result[0];
        foreach ($this->schema->getLevels() as $schemaLevel) {
            if (!isset($row[$schemaLevel . '_id'])) {
                $row[$schemaLevel . '_id'] = 0;
            }
        }

        $vehicle = $this->findOneByLevelIds($row, VF_Vehicle_Finder::INCLUDE_PARTIALS);

        self::$IDENTITY_MAP_FINDBYLEVEL[$level . $id] = $vehicle;
        return $vehicle;
    }

    function findByRangeIds($levelIds)
    {
        $vehicles = array();
        $levelIds = $this->ensureYearStartAndYearEndArentBackwards($levelIds);

        $year_start = $levelIds['year_start'];
        $year_end = $levelIds['year_end'];

        unset($levelIds['year_start']);
        unset($levelIds['year_end']);

        for ($year = $year_start; $year <= $year_end; $year++) {
            $theseVehicles = $this->findByLevelIds($levelIds + array('year' => $year));
            $vehicles = array_merge($vehicles, $theseVehicles);
        }
        return $vehicles;
    }

    function findByRange($levels)
    {
        $vehicles = array();
        $year_start = $levels['year_start'];
        $year_end = $levels['year_end'];

        unset($levels['year_start']);
        unset($levels['year_end']);

        for ($year = $year_start; $year <= $year_end; $year++) {
            $theseVehicles = $this->findByLevels($levels + array('year' => $year));
            $vehicles = array_merge($vehicles, $theseVehicles);
        }
        return $vehicles;
    }

    /**
     * Find vehicles which match some criteria, for example searching for
     * array(
     *     'make'=>'honda',
     *     'year'=>'2000'
     * )
     * would return "Honda Civic 2000", "Honda Fit 2000", but not "Honda Civic 2001"
     *
     * @param $levels array conjunction of critera Ex: ('make'=>'honda','year'=>'2000')
     * @param boolean $includePartials set to true to include 'partial' vehicles in the result set
     * @param $limit - limit of # of vehicles to return
     * @param $offset - offset for limit clause
     * @return array of Vehicle that meet the critera
     */
    function findByLevels($levels, $includePartials = false, $limit=null, $offset=null)
    {
        $levels = $this->checkForInvalidLevels($levels);

        $select = $this->select()
            ->from('elite_' . $this->schema->id() . '_definition')
            ->joinAndSelectLevels('elite_' . $this->schema->id() . '_definition', array_keys($levels), $this->schema);

        foreach ($levels as $level => $value) {

            if (strpos($value, '-') || false !== strpos($value, '*')) {
                $value = $this->regexifyValue($value);
                $where = $this->getReadAdapter()->quoteInto('elite_level_' . $this->schema->id() . '_' . $this->inflect($level) . '.title RLIKE ?', '^' . $value . '$');
                $select->where($where);
            } else {
                $select->where('elite_level_' . $this->schema->id() . '_' . $this->inflect($level) . '.title = ?', $value);
            }
        }

        if (!$includePartials) {
            foreach ($this->schema->getLevels() as $level) {
                $select->where('elite_' . $this->schema->id() . '_definition.' . $this->inflect($level) . '_id != 0');
            }
        }

        if($limit) {
            $select->limit($limit,$offset);
        }

        $result = $this->query($select)->fetchAll(Zend_Db::FETCH_OBJ);

        $levelsToRemove = array();
        foreach ($this->schema->getLevels() as $levelInSchema) {
            if ($includePartials && !in_array($levelInSchema, array_keys($levels))) {
                $levelsToRemove[] = $levelInSchema;
            }
        }


        $return = array();
        foreach ($result as $row) {
            foreach ($levelsToRemove as $levelToRemove) {
                unset($row->$levelToRemove);
                unset($row->{$levelToRemove . '_id'});
            }
            $return[] = new VF_Vehicle($this->schema, $row->id, $row);
        }

        return $return;
    }

    function findDistinct($levelsToSelect, $where=array())
    {
        $select = $this->select();

        if(isset($where['product_id'])) {
            $select->from('elite_' . $this->schema->id() . '_mapping', $levelsToSelect);
        } else {
            $select->from('elite_' . $this->schema->id() . '_definition', $levelsToSelect);
        }

        foreach ($this->schema->getLevels() as $level) {
            if(isset($where[$level])) {
                $select->where($this->inflect($level) . ' = ?', $where[$level]);
            }
        }

        foreach($levelsToSelect as $levelToSelect) {
            $select->group($this->inflect($levelsToSelect));
        }

        if(isset($where['product_id'])) {
            $select->where('entity_id = ?', $where['product_id']);
        }

        $result = $this->query($select)->fetchAll(Zend_Db::FETCH_ASSOC);

        $return = array();
        foreach ($result as $row) {
            $return[] = new VF_Vehicle($this->schema, 0, $row, false, $row);
        }

        return $return;
    }

    function regexifyValue($value)
    {
        $value = str_replace(array('-', '*'), array('##hyphen##', '##dash##'), $value);
        $value = preg_quote($value);
        $value = str_replace(array('##hyphen##', '##dash##'), array('-', '*'), $value);

        $value = preg_replace('#\*#', '.*', $value);
        $value = preg_replace('#[ -]#', '[ -]', $value);
        return $value;
    }

    /**
     * Just like findByLevels() but filters on the integer IDs of the levels, instead of their string titles
     *
     * @param $levelIds array conjunction of critera Ex: ('make'=>1,'year'=>1)
     * @param boolean $mode set to true to include 'partial' vehicles in the result set
     * @param $limit - limit of # of vehicles to return
     * @param $offset - offset for limit clause
     * @return array of Vehicle that meet the critera
     */
    function findByLevelIds($levelIds, $mode = false, $limit=null, $offset=null)
    {
        $levelIds = $this->cleanupLevelIds($levelIds, $mode);
        $levelIds = $this->specifyPartial($levelIds, $mode);
        $levelsToSelect = $this->levelsToSelect($levelIds, $mode);

        $select = $this->select()
            ->from('elite_' . $this->schema->id() . '_definition')
            ->joinAndSelectLevels(VF_Select::DEFINITIONS, $levelsToSelect, $this->schema)
            ->whereLevelIdsEqual($levelIds);

        if (self::INCLUDE_PARTIALS != $mode) {
            foreach ($this->schema->getLevels() as $level) {
                if (self::INCLUDE_PARTIALS != $mode || (isset($levelIds[$level]) && $levelIds[$level])) {
                    $level = str_replace(' ', '_', $level);
                    $select->where('elite_' . $this->schema->id() . '_definition.' . $level . '_id != 0');
                }
            }
        }

        if($limit) {
            $select->limit($limit,$offset);
        }

        $result = $this->query($select)->fetchAll(Zend_Db::FETCH_OBJ);

        $return = array();
        foreach ($result as $row) {
            foreach ($this->schema->getLevels() as $level) {
                if (self::INCLUDE_PARTIALS == $mode && (!in_array($level, $levelsToSelect)) && $row->{$level . '_id'}) {
                    foreach ($this->schema->getNextLevelsIncluding($level) as $level) {
                        $row->{$level . '_id'} = 0;
                        $row->{$level} = '';
                    }
                    $vehicle = new VF_Vehicle($this->schema, $row->id, $row);
                    return array($vehicle);
                }

                if ((!isset($levelIds[$level]) || !$levelIds[$level]) && $mode) {
                    $row->{$level . '_id'} = 0;
                    $row->{$level} = '';
                }
                if (isset($levelIds[$level]) && !$levelIds[$level] && $row->{$level . '_id'}) {
                    continue;
                }

                if ((!$mode || self::INCLUDE_PARTIALS == $mode) && (!isset($levelIds[$level]) || !$levelIds[$level]) && !$row->{$level . '_id'}) {
                    continue;
                }
            }

            $vehicle = new VF_Vehicle($this->schema, $row->id, $row);
            array_push($return, $vehicle);
        }
        return $return;
    }

    /** Count # of vehicles that match the query, [same interface as findByLevelIds() without the limit & offset] */
    function countByLevelIds($levelIds, $mode=false)
    {
        $levelIds = $this->cleanupLevelIds($levelIds, $mode);
        $levelIds = $this->specifyPartial($levelIds, $mode);
        $levelsToSelect = $this->levelsToSelect($levelIds, $mode);

        $select = $this->select()
            ->from('elite_' . $this->schema->id() . '_definition', new Zend_Db_Expr('count(*)'))
            ->joinAndSelectLevels(VF_Select::DEFINITIONS, $levelsToSelect, $this->schema)
            ->whereLevelIdsEqual($levelIds);

        if (self::INCLUDE_PARTIALS != $mode) {
            foreach ($this->schema->getLevels() as $level) {
                if (self::INCLUDE_PARTIALS != $mode || (isset($levelIds[$level]) && $levelIds[$level])) {
                    $level = str_replace(' ', '_', $level);
                    $select->where('elite_' . $this->schema->id() . '_definition.' . $level . '_id != 0');
                }
            }
        }

        return $this->query($select)->fetchColumn();
    }

    /**
     * @param array ('make'=>'honda','year'=>'2000') conjunction of critera
     * @param $mode - set to true to match "partial" vehicles
     * @return VF_Vehicle or false
     */
    function findOneByLevels($levels, $mode = false)
    {
        $vehicles = $this->findByLevels($levels, $mode);
        return isset($vehicles[0]) ? $vehicles[0] : false;
    }

    /**
     * @param array ('make'=>1,'year'=>1) conjunction of critera
     * @return VF_Vehicle or false
     */
    function findOneByLevelIds($levelIds, $mode = false)
    {
        $vehicles = $this->findByLevelIds($levelIds, $mode);
        return isset($vehicles[0]) ? $vehicles[0] : false;
    }

    /** @deprecated this is an ambiguous query now */
    function findByLeaf($leaf_id)
    {
        #throw new Exception('this is deprecated. Do NOT call it.');
        return $this->findByLevel($this->schema->getLeafLevel(), $leaf_id);
    }

    function vehicleExists(array $levelTitles)
    {
        return 0 != count($this->findByLevels($levelTitles));
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

    function getColumns()
    {
        $columns = array();
        $levels = $this->schema->getLevels();

        foreach ($levels as $level) {
            $columns[$level . '_id'] = 'elite_level_' . $this->schema->id() . '_' . $level . '.id';
            $columns[$level] = 'elite_level_' . $this->schema->id() . '_' . $level . '.title';
        }
        return $columns;
    }

    function addJoins(Zend_Db_Select $select, $noRoot = false)
    {
        $joins = '';
        $levels = $this->schema->getLevels();

        foreach ($levels as $level) {
            $condition = sprintf('`elite_level_%2$d_%1$s`.`id` = `elite_%2$d_definition`.`%1$s_id`', $level, $this->schema->id());
            $select->joinLeft('elite_level_' . $this->schema->id() . '_' . $level, $condition);
        }
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

    protected function cols($stopLevel = false)
    {
        $cols = array();
        foreach ($this->schema->getLevels() as $level) {
            array_push($cols, $level . '_id');
            if ($stopLevel && $level == $stopLevel) {
                return $cols;
            }
        }
        return $cols;
    }

    function inflect($identifier)
    {
        return str_replace(' ', '_', $identifier);
    }

    function select()
    {
        return new VF_Select($this->getReadAdapter());
    }

    function checkForInvalidLevels($levels)
    {
        foreach ($levels as $level => $value) {
            if (!in_array($level, $this->schema->getLevels())) {
                throw new Exception('Invalid level ' . $level);
            }
        }
        return $levels;
    }

    /** Figures out which levels need to be selected based on the level IDs being searched & what mode */
    function levelsToSelect($levelIds, $mode)
    {
        $levelsToSelect = array();
        foreach ($this->schema->getLevels() as $level) {
            if (self::INCLUDE_PARTIALS == $mode && !$levelIds[$level]) {
                continue;
            }
            if (self::INCLUDE_PARTIALS == $mode && !$levelIds[$level]) {
                continue;
            }
            array_push($levelsToSelect, $level);
        }
        return $levelsToSelect;
    }

    /** Takes an array like array('make_id'=>5) and converts it to array('make'=>5) */
    function cleanupLevelIds($levelIds, $mode)
    {
        foreach ($this->schema->getLevels() as $level) {
            if (isset($levelIds[$level . '_id'])) {
                $levelIds[$level] = $levelIds[$level . '_id'];
                unset($levelIds[$level . '_id']);
            }
        }
        return $levelIds;
    }

    /** Depending on the $mode, will replace missing levels with 0 or false */
    function specifyPartial($levelIds, $mode)
    {
        foreach ($this->schema->getLevels() as $level) {
            if (isset($levelIds[$level])) {
                continue;
            }
            if (self::INCLUDE_PARTIALS == $mode) {
                $levelIds[$level] = 0;
            } else {
                $levelIds[$level] = false;
            }
        }
        return $levelIds;
    }

    function ensureYearStartAndYearEndArentBackwards($levelIds)
    {
        if ($levelIds['year_start'] > $levelIds['year_end']) {
            $year_end = $levelIds['year_start'];
            $year_start = $levelIds['year_end'];

            $levelIds['year_start'] = $year_start;
            $levelIds['year_end'] = $year_end;
        }
        return $levelIds;
    }
}