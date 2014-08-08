<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_Observer_Exporter_Mappings_CSV extends VF_Note_Observer_Exporter_Mappings
{
    function doCols()
    {
        return ',notes';
    }

    function doRow($row)
    {
        $select = $this->getReadAdapter()->select()
            ->from('elite_mapping_notes')
            ->where('fit_id = ?', $row->id)
            ->joinLeft('elite_note', 'elite_note.id = elite_mapping_notes.note_id', array('code'));

        $noteCodes = array();
        $result = $select->query();
        while ($noteRow = $result->fetch()) {
            array_push($noteCodes, $noteRow['code']);
        }
        return ',"' . implode(',', $noteCodes) . '"';
    }

    /** @return Zend_Db_Adapter_Abstract */
    protected function getReadAdapter()
    {
        return VF_Singleton::getInstance()->getReadAdapter();
    }
}