<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_Import_ProductFitments_CSV_Export extends VF_Import_VehiclesList_CSV_Export
{

    protected $product_table;

    /** @var VF_Schema */
    protected $schema;

    function cols()
    {
        $return = $this->col('sku');
        $return .= $this->col('universal');
        $return .= parent::cols();
        $return .= $this->doCols();
        return $return;
    }

    function rows($stream)
    {
        $rowResult = $this->rowResult();
        $i = 0;
        while ($row = $rowResult->fetch(Zend_Db::FETCH_OBJ)) {
            $i++;
            fwrite($stream, $this->col($row->sku));
            fwrite($stream, $this->col($row->universal));
            fwrite($stream, $this->definitionCells($row));
            fwrite($stream, $this->doRow($row));
            fwrite($stream, "\n");
        }
    }

    function rowResult()
    {
        $this->getReadAdapter()->getConnection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $select = $this->getReadAdapter()->select()
            ->from($this->schema()->mappingsTable(), array('id', 'universal'));
        foreach ($this->schema->getLevels() as $level) {
            $levelTable = $this->schema()->levelTable($level);
            $condition = sprintf('%s.id = ' . $this->schema()->mappingsTable() . '.%s_id', $levelTable, $level);
            $select->joinLeft($levelTable, $condition, array($level => 'title'));
        }

        $table = array('p' => $this->getProductTable());
        $condition = 'p.' . $this->getProductIdField() . ' = ' . $this->schema()->mappingsTable() . '.entity_id';
        $columns = array('sku'=>$this->getProductSkuField());
        $select->joinLeft($table, $condition, $columns);

        return $this->query($select);
    }

    function setProductTable($tableName)
    {
        $this->product_table = $tableName;
        return $this;
    }

    function getProductTable()
    {
        if(isset($this->product_table)) {
            return $this->product_table;
        }
        $resource = new Mage_Catalog_Model_Resource_Eav_Mysql4_Product;
        $table = $resource->getTable('catalog/product');
        return $table;
    }

    function getProductSkuField()
    {
        return isset($this->product_sku_field) ? $this->product_sku_field : 'sku';
    }

    function setProductSkuField($product_sku_field)
    {
        $this->product_sku_field = $product_sku_field;
        return $this;
    }

    function getProductIdField()
    {
        return isset($this->product_id_field) ? $this->product_id_field : 'entity_id';
    }

    function setProductIdField($product_id_field)
    {
        $this->product_id_field = $product_id_field;
        return $this;
    }

    private function doCols()
    {
        $exporter = new VF_Note_Observer_Exporter_Mappings_CSV();
        return $exporter->doCols();
    }

    private function doRow($row)
    {
        $exporter = new VF_Note_Observer_Exporter_Mappings_CSV;
        return $exporter->doRow($row);
    }
}
