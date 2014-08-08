<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_Import_VehiclesList_CSV_Import extends VF_Import
{

    /** @var array keyed by level name Ex. $count_added_by_level['make'] */
    protected $count_added_by_level = array();
    protected $start_count_added_by_level = array();
    protected $stop_count_added_by_level = array();
    /** @var integer */
    protected $start_count_vehicles, $stop_count_vehicles;
    /** @var integer */
    protected $invalid_vehicle_count = 0;
    /** @var integer corresponds to the row # in the CSV we are reading in */
    protected $row_number = 0;
    /** @var bool we set this flag if we detect year_start & year_end, year_range, etc. */
    protected $has_range = false;

    function import()
    {
        $this->log('Import Started', Zend_Log::INFO);
        $this->getReadAdapter()->beginTransaction();

        try {
            $this->startCountingAdded();
            $this->getFieldPositions();
            $this->doImport();
            $this->stopCountingAdded();
        } catch (Exception $e) {
            $this->getReadAdapter()->rollBack();
            $this->log('Import Cancelled & Reverted Due To Critical Error: ' . $e->getMessage() . $e->getTraceAsString(), Zend_log::CRIT);
            throw $e;
        }

        $this->getReadAdapter()->commit();
        $this->log('Import Completed', Zend_Log::INFO);
        return $this;
    }

    function insertRowsIntoTempTable()
    {
        $this->cleanupTempTable();

        $streamFile = sys_get_temp_dir() . '/import' . md5(uniqid());
        $stream = fopen($streamFile, 'w');

        while ($row = $this->getReader()->getRow()) {
            $row = $this->trimSpaces($row);
            $this->row_number++;

            $values = $this->getLevelsArray($row);
            if ('1' !== $this->getFieldValue('universal', $row) && '0' !== $this->getFieldValue('universal', $row) &&
                $this->isInvalidVehicle($values)
            ) {
                $this->invalid_vehicle_count++;
                continue;
            }

            if (null === $this->getFieldValue('universal', $row) && !$values) {
                continue;
            }

            if (!$this->has_range && !$this->rowContainsWildcards($row)) {
                // HAS NO WILD CARDS, DO THE FAST WAY
                if (false !== $this->getFieldPosition('sku')) {
                    $values['sku'] = $this->getFieldValue('sku', $row);
                    $values['universal'] = $this->getFieldValue('universal', $row);
                }

                $this->insertIntoTempStream($stream, $row, $values);
                continue;
            }

            // HAS WILD CARDS, DO THIS ITS SLOWER THOUGH
            $combinations = $this->getCombinations($values, $row);
            foreach ($combinations as $combination) {
                $this->insertIntoTempStream($stream, $row, $combination);
            }
        }

        $this->importFromTempStream($streamFile);
    }

    function isInvalidVehicle($levels)
    {
        $invalid = false;
        foreach ($this->getSchema()->getLevels() as $level) {
            if ($this->isInvalidLevel($level, $levels)) {
                $invalid = true;
            }
        }
        return $invalid;
    }

    function isInvalidLevel($level, $levels)
    {
        if (isset($levels[$level]) && $levels[$level]) {
            return false;
        }
        if (isset($levels[$level . '_range']) && $levels[$level . '_range']) {
            return false;
        }
        if (isset($levels[$level . '_start']) && $levels[$level . '_start'] &&
            isset($levels[$level . '_end']) && $levels[$level . '_end']
        ) {
            return false;
        }
        return true;
    }

    function rowContainsWildcards($row)
    {
        foreach ($row as $field) {
            if (false !== strpos($field, ',') || false !== strpos($field, '*') || false !== strpos($field, '{{all}}')) {
                return true;
            }
        }
        return false;
    }

    function importFromTempStream($streamFile)
    {
        try {
            $query = 'LOAD DATA INFILE ' . $this->getReadAdapter()->quote($streamFile) . '
            INTO TABLE elite_import FIELDS TERMINATED BY \',\'  ENCLOSED BY \'"\'
            (' . $this->getSchema()->getLevelsString() . ',sku,universal,line,note_message,notes,price)
            ';
            $this->getReadAdapter()->query($query);
        } catch (Exception $e) {
            /** If user does not have FILE privileges in MySql we'll have to use slow insert statements. */
            $h = fopen($streamFile, 'r');
            while ($row = fgetcsv($h)) {
                $newRow = array();
                $i = 0;
                foreach ($this->getSchema()->getLevels() as $level) {
                    $newRow[$level] = $row[$i];
                    $i++;
                }
                $newRow = $newRow + array(
                    'sku' => $row[$i + 0],
                    'universal' => $row[$i + 1],
                    'line' => $row[$i + 2],
                    'note_message' => $row[$i + 3],
                    'notes' => isset($row[$i + 4]) ? $row[$i + 4] : '',
                    'price' => isset($row[$i + 5]) ? $row[$i + 5] : ''
                );

                $this->insertIntoTempTable($newRow, $newRow);
            }
        }
    }

    function runDeprecatedImports()
    {
        $this->getReader()->rewind();

        $this->getReader()->getRow(); // pop fields
        $this->row_number = 0;
        while ($row = $this->getReader()->getRow()) {
            $this->importRow($row);
        }
    }

    function insertIntoTempStream($stream, $row, $combination)
    {
        $combination['line'] = $this->row_number;
        $combination['note_message'] = $this->getFieldValue('note_message', $row);
        $combination['notes'] = $this->getFieldValue('notes', $row);
        $combination['universal'] = $this->getFieldValue('universal', $row) ? $this->getFieldValue('universal', $row) : 0;
        $combination['price'] = $this->getFieldValue('price', $row);

        fputcsv($stream, $combination);
    }

    function insertIntoTempTable($row)
    {
        $this->getReadAdapter()->insert('elite_import', $this->columns($row));
    }

    function importRow($row)
    {
        $this->row_number++;
    }

    /** @deprecated */
    function oldImportRow($row)
    {
        $values = $this->getLevelsArray($row);
        if (!$values) {
            return;
        }
        $combinations = $this->getCombinations($values, $row);

        foreach ($combinations as $combination) {
            if ($this->fieldsAreBlank($combination)) {
                $this->doImportRow($row, false);
                continue;
            }

            $vehicle = $this->vehicleFinder()->findOneByLevels($combination);
            $this->doImportRow($row, $vehicle);
        }
    }

    function vehicleFinder()
    {
        return new VF_Vehicle_Finder($this->getSchema());
    }

    /** @return boolean true only if all field names in the combination are blank */
    function fieldsAreBlank($combination)
    {
        foreach ($this->schema()->getLevels() as $level) {
            if ($combination[$level] == '') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $row
     * @param VF_Vehicle|boolean the vehicle, false if none (for example, when setting a product as universal)
     */
    function doImportRow($row, $vehicle)
    {

    }

    function trimSpaces($values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = trim($value);
        }
        return $values;
    }

    /** Reset # of added Vehicles, Makes, Years, etc. each to Zero */
    function resetCountAdded()
    {
        $this->start_count_vehicles = 0;
        $this->stop_count_vehicles = 0;

        $this->count_added_by_level = array();
        $this->start_count_added_by_level = array();
        $this->stop_count_added_by_level = array();
        foreach ($this->getSchema()->getLevels() as $level) {
            $this->count_added_by_level[$level] = 0;
            $this->start_count_added_by_level[$level] = 0;
            $this->stop_count_added_by_level[$level] = 0;
        }
    }

    /** Probe how many Make,Model,Year there are before the import */
    function startCountingAdded()
    {
        $this->resetCountAdded();
        $this->startCountingAddedLevels();
        $this->startCountingAddedVehicles();
        $this->doStartCountingAdded();
    }

    function doStartCountingAdded()
    {

    }

    /** Probe how many Make,Model,Year there are before the import */
    function startCountingAddedLevels()
    {
        foreach ($this->getSchema()->getLevels() as $level) {
            $select = $this->getReadAdapter()->select()->from('elite_level_' . $this->getSchema()->id() . '_' . str_replace(' ', '_', $level), 'count(*)');
            $result = $select->query()->fetchColumn();
            $this->start_count_added_by_level[$level] = $result;
        }
    }

    function startCountingAddedVehicles()
    {
        $select = $this->getReadAdapter()->select()->from($this->getSchema()->definitionTable(), 'count(*)');
        $result = $select->query()->fetchColumn();
        $this->start_count_vehicles = $result;
    }

    /** Probe how many Make,Model,Year there are after the import */
    function stopCountingAdded()
    {
        $this->stopCountingAddedLevels();
        $this->stopCountingAddedVehicles();
        $this->doStopCountingAdded();
    }

    function doStopCountingAdded()
    {

    }

    function stopCountingAddedLevels()
    {
        foreach ($this->getSchema()->getLevels() as $level) {
            $select = $this->getReadAdapter()->select()->from('elite_level_' . $this->getSchema()->id() . '_' . str_replace(' ', '_', $level), 'count(*)');
            $result = $select->query()->fetchColumn();
            $this->stop_count_added_by_level[$level] = $result;
            $this->count_added_by_level[$level] = $this->stop_count_added_by_level[$level] - $this->start_count_added_by_level[$level];
        }
    }

    function stopCountingAddedVehicles()
    {
        $select = $this->getReadAdapter()->select()->from($this->getSchema()->definitionTable(), 'count(*)');
        $result = $select->query()->fetchColumn();
        $this->stop_count_vehicles = $result;
    }

    /**
     * @param string $level
     * @return integer number of [make,model,or year] values that have been added during this import
     */
    function getCountAddedByLevel($level)
    {
        if (!isset($this->count_added_by_level[$level])) {
            return 0;
        }
        return (int)$this->count_added_by_level[$level];
    }

    function getCountAddedVehicles()
    {
        return $this->stop_count_vehicles - $this->start_count_vehicles;
    }

    /**
     * @var array $row from import file
     * @return array keyed by level names (make,model,year) or ranges (year_start,year_end)
     */
    function getLevelsArray($row)
    {
        $fieldPositions = $this->getFieldPositions();
        $levels = array();
        foreach ($this->getSchema()->getLevels() as $level) {
            if (isset($fieldPositions[$level . '_start']) && isset($fieldPositions[$level . '_end'])) {
                $this->has_range = true;
                $levels[$level . '_start'] = $this->getFieldValue($level . '_start', $row) ? $this->getFieldValue($level . '_start', $row) : $this->getFieldValue($level . '_end', $row);
                $levels[$level . '_end'] = $this->getFieldValue($level . '_end', $row) ? $this->getFieldValue($level . '_end', $row) : $this->getFieldValue($level . '_start', $row);
            } else if (isset($fieldPositions[$level . '_range'])) {
                $this->has_range = true;
                $levels[$level . '_range'] = $this->getFieldValue($level . '_range', $row);
            } elseif (isset($fieldPositions[$level])) {
                $levels[$level] = $this->getFieldValue($level, $row);
                if (!$levels[$level] && !$this->getFieldValue('universal', $row)) {
                    $this->log('Line(' . $this->row_number . ') Blank ' . ucfirst($level), Zend_Log::NOTICE);
                    $levels[$level] = false;
                }
            } else {
                $levels[$level] = 'Base';
            }
        }
        return $levels;
    }

    function getCombinations($values, $row)
    {
        $combiner = new VF_Import_Combiner($this->getSchema(), $this->getConfig());
        $combinations = $combiner->getCombinations($values);
        if ($combiner->getError()) {
            $this->log('Line(' . $this->row_number . ') ' . $combiner->getError(), Zend_Log::NOTICE);
            $this->invalid_vehicle_count++;
        }
        $combinations = $this->doGetCombinations($combinations, $row);
        return $combinations;
    }

    function doGetCombinations($combinations, $row)
    {
        return $combinations;
    }

}