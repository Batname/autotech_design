<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_BaseExport
{
    function schema()
    {
        return new VF_Schema();
    }

    function rowResult()
    {
        $select = $this->getReadAdapter()->select()
            ->from(array('d' => $this->schema()->definitionTable()));
        foreach ($this->schema()->getLevels() as $level) {
            $table = $this->schema()->levelTable($level);
            $condition = sprintf('%s.id = d.%s_id', $table, $level);
            $select
                ->joinLeft($table, $condition, array($level => 'title'))
                ->where('d.' . $level . '_id != 0');
        }
        return $this->query($select);
    }

    /** @return Zend_Db_Statement_Interface */
    function query($sql)
    {
        return $this->getReadAdapter()->query($sql);
    }

    /** @return Zend_Db_Adapter_Abstract */
    function getReadAdapter()
    {
        return VF_Singleton::getInstance()->getReadAdapter();
    }
}