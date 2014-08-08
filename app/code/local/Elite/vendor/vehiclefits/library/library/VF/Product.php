<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Product
{
    /** @var Collection of VF_Vehicle */
    protected $fits = NULL;
    /** @var VF_Vehicle the customer has associated */
    protected $fit;
    /** @var Zend_Config */
    protected $config;

    protected $id;

    function setId($id)
    {
        $this->id = $id;
    }

    function getId()
    {
        return $this->id;
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

    function getFitModels()
    {
        $fits = $this->getFits();
        $return = array();
        foreach ($fits as $fitRow) {
            $fit = $this->createFitFromRow($fitRow);
            array_push($return, $fit);
        }
        return $return;
    }

    /** Get a result set for the fits for this product */
    function getFits()
    {
        if (!is_null($this->fits)) {
            return $this->fits;
        }
        if ($productId = (int)$this->getId()) {
            $this->fits = $this->doGetFits($productId);
            return $this->fits;
        }
        return array();
    }

    function customPrice($vehicle)
    {
        $select = $this->getReadAdapter()->select();
        $select->from(array('m' => $this->getSchema()->mappingsTable()), array('price'));

        foreach ($vehicle->toValueArray() as $parentType => $parentId) {
            if (!in_array($parentType, $this->getSchema()->getLevels())) {
                throw new VF_Level_Exception($parentType);
            }
            if (!(int)$parentId) {
                continue;
            }
            $select->where(sprintf('m.`%s_id` = ?', $parentType), $parentId);
        }

        $select->where('`entity_id` = ?', $this->getId());

        $price = $this->query($select)->fetchColumn();
        return (!$price) ? null : $price;
    }

    function getOrderBy()
    {
        $schema = new VF_Schema();
        $levels = $schema->getLevels();
        $c = count($levels);
        $sql = '';
        for ($i = 0; $i <= $c - 1; $i++) {
            $sql .= '`' . $levels[$i] . '`' . ($i < $c - 1 ? ',' : '');
        }
        return $sql;
    }

    public static function getJoins()
    {
        $joins = '';

        $schema = new VF_Schema();
        $levels = $schema->getLevels();

        $c = count($levels);
        for ($i = 0; $i <= $c - 1; $i++) {
            $joins .= sprintf(
                '
                LEFT JOIN
                    `elite_%1$s`
                ON
                    `elite_%1$s`.`id` = `".$this->getSchema()->mappingsTable()."`.`%1$s_id`
                ',
                $levels[$i]
            );
        }
        return $joins;
    }

    /**
     * Add one or more fitment(s) described by an array of level IDs
     *
     * Examples -  add make 5 and all its children:
     * array( 'make' => 5 )
     *
     *  ...   is the same as:
     * array( 'make' => 5, 'model' => 0 )
     *
     * ... or add a individual fit:
     * array( 'make' => 5, 'model' => 3, 'year' => 4 )
     *
     * ... is the same as
     * array( 'year' => 4 )
     *
     * @param array fitToAdd - fitment to add represented as an array keyed by level name [string]
     * @return integer ID of fitment row created
     */
    function addVafFit(array $fitToAdd)
    {
        $vehicles = $this->vehicleFinder()->findByLevelIds($fitToAdd);
        $mapping_id = null;
        foreach ($vehicles as $vehicle) {
            $mapping_id = $this->insertMapping($vehicle);
        }
        return $mapping_id;
    }

    function vehicleFinder()
    {
        return new VF_Vehicle_Finder($this->getSchema());
    }

    function insertMapping(VF_Vehicle $vehicle)
    {
        $mapping = new VF_Mapping($this->getId(), $vehicle);
        return $mapping->save();
    }

    function deleteVafFit($mapping_id)
    {
        $sql = sprintf("DELETE FROM `" . $this->getSchema()->mappingsTable() . "` WHERE `id` = %d", (int)$mapping_id);
        $this->query($sql);

        if (file_exists(ELITE_PATH . '/Vafnote')) {
            $sql = sprintf("DELETE FROM `elite_mapping_notes` WHERE `fit_id` = %d", (int)$mapping_id);
            $this->query($sql);
        }
    }

    /** @return boolean */
    function isUniversal()
    {
        $sql = sprintf(
            "
            SELECT
                count( * )
            FROM
                `" . $this->getSchema()->mappingsTable() . "`
		    WHERE
		        `entity_id` = %d
		    AND
		        `universal` = 1
		    ",
            (int)$this->getId()
        );
        $result = $this->query($sql);
        $count = $result->fetchColumn();
        return $count == 0 ? false : true;
    }

    /** @param boolean */
    function setUniversal($universal)
    {
        if (!$universal) {
            $query = sprintf("DELETE FROM " . $this->getSchema()->mappingsTable() . " WHERE universal = 1 AND entity_id = %d", $this->getId());
            $r = $this->query($query);
            return;
        }
        $sql = sprintf("REPLACE INTO `" . $this->getSchema()->mappingsTable() . "`
                        (`universal`,`entity_id`)
                        VALUES
                        (%d,%d)",
            1,
            (int)$this->getId());
        $this->query($sql);
    }

    function getName($name)
    {
        $this->setFitFromGlobalIfNoLocalFitment();
        if (!$this->rewritesOn() || !$this->fitsSelection()) {
            return $name;
        }
        $template = $this->getConfig()->seo->productNameTemplate;
        if (empty($template)) {
            $template = '_product_ for _vehicle_';
        }

        $find = array('_product_', '_vehicle_');
        $replace = array($name, (string)$this->currentlySelectedFit()->getFirstVehicle());
        return str_replace($find, $replace, $template);
    }

    function setFitFromGlobalIfNoLocalFitment()
    {
        $selection = VF_Singleton::getInstance()->vehicleSelection();
        if (!$this->fit && !$selection->isEmpty()) {
            $this->fit = $selection;
        }
    }

    function rewritesOn()
    {
        return $this->getConfig()->seo->rewriteProductName;
    }

    function globalRewritesOn()
    {
        return $this->getConfig()->seo->globalRewrites;
    }

    function setCurrentlySelectedFit($fit)
    {
        $this->fit = new VF_Vehicle_Selection(array($fit));
    }

    function currentlySelectedFit()
    {
        $this->setFitFromGlobalIfNoLocalFitment();
        if ($this->fit) {
            return $this->fit;
        } else {
            return new VF_Vehicle_Selection();
        }
    }

    function fitsSelection()
    {
        $currentVehicleSelection = $this->currentlySelectedFit();
        if ($currentVehicleSelection->isEmpty()) {
            return false;
        }
        $vehicle = $currentVehicleSelection->getFirstVehicle();
        return $this->fitsVehicle($vehicle);
    }

    function fitsVehicle($vehicle)
    {
        $select = $this->getReadAdapter()->select()
            ->from($this->getSchema()->mappingsTable(), array('count(*)'))
            ->where('entity_id = ?', $this->getId());
        $params = $vehicle->toValueArray();
        foreach ($params as $param => $value) {
            $select->where($param .= '_id = ?', $value);
        }

        $count = $select->query()->fetchColumn();
        return 0 != $count;
    }

    function isInEnabledCategory(Elite_Vaf_Model_Catalog_Category_Filter $filter, $categoryIds)
    {
        foreach ($categoryIds as $categoryId) {
            if ($filter->shouldShow($categoryId)) {
                return true;
            }
        }
        return false;
    }

    function getMappingId(VF_Vehicle $vehicle)
    {
        $schema = new VF_Schema;
        $select = $this->getReadAdapter()->select()
            ->from($this->getSchema()->mappingsTable(), 'id')
            ->where($schema->getLeafLevel() . '_id = ?', $vehicle->getLeafValue())
            ->where('entity_id = ?', $this->getId());
        return $select->query()->fetchColumn();
    }

    /**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    function duplicate()
    {
        $schema = new VF_Schema();
        $vehicleFinder = new VF_Vehicle_Finder($schema);
        $leaf = $schema->getLeafLevel() . '_id';

        $newProduct = parent::duplicate();
        foreach ($this->getFits() as $fit) {
            print_r($fit);
            exit;
            $vehicle = $vehicleFinder->findByLeaf($fit->$leaf);
            $newProduct->insertMapping($vehicle);
        }
        if ($this->isUniversal()) {
            $newProduct->setUniversal(true);
        }
        return $newProduct;
    }

    /**
     * @param Elite_Vaf_Model_Abstract - if is an "aggregrate" of fits ( iterate and add it's children )
     */
    function doAddFit($entity)
    {
        $vehicleFinder = new VF_Vehicle_Finder(new VF_Schema);
        $params = array($entity->getType() => $entity->getTitle());
        $vehicles = $vehicleFinder->findByLevels($params);
        return $vehicles;
    }

    function createFitFromRow($row)
    {
        $schema = new VF_Schema();
        return new VF_Vehicle($schema, $row->id, $row);
    }

    function doGetFits($productId)
    {
        $select = new VF_Select($this->getReadAdapter());
        $select->from($this->getSchema()->mappingsTable())
            ->joinAndSelectLevels()
            ->where('entity_id=?', $productId);
        $result = $this->query($select);

        $fits = array();
        while ($row = $result->fetchObject()) {
            if ($row->universal) {
                continue;
            }
            $fits[] = $row;
        }
        return $fits;
    }

    function getSchema()
    {
        return new VF_Schema();
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