<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level implements VF_Configurable
{

    protected $id;
    protected $title;
    protected $type;
    /** @var VF_Level_Finder */
    protected $finder;
    /** @var VF_Schema */
    protected $schema;
    /** @var Zend_Config */
    protected $config;

    function __construct($type, $id = 0, $schema = null)
    {
        $this->schema = $schema ? $schema : new VF_Schema;
        if ($id && !in_array($type, $this->getSchema()->getLevels())) {
            throw new VF_Level_Exception_InvalidLevel('[' . $type . '] is an invalid level');
        }
        $this->type = $type;
        $this->id = $id;
    }

    function identityMap()
    {
        return VF_Level_IdentityMap::getInstance();
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

    /** @return VF_Level_Finder */
    function getFinder()
    {
        if (!($this->finder instanceof VF_Level_Finder)) {
            $this->finder = new VF_Level_Finder($this->getSchema());
        }
        $this->finder->setConfig($this->getConfig());
        return $this->finder;
    }

    function getId()
    {
        return $this->id;
    }

    function setId($id)
    {
        if (0 == $this->id) {
            $this->id = $id;
        } else {
            throw new Exception('cannot set id if its previously already set');
        }
    }

    function setTitle($title)
    {
        $this->title = trim($title);
        return $this;
    }

    function getTitle()
    {
        return (string)$this->title;
    }

    function getLabel()
    {
        return $this->getType();
    }

    function getNextLevel()
    {
        return $this->getSchema()->getNextLevel($this->getType());
    }

    function getPrevLevel()
    {
        return $this->getSchema()->getPrevLevel($this->getType());
    }

    function createEntity($level, $id = 0)
    {
        return new VF_Level($level, $id);
    }

    function getType()
    {
        return $this->type;
    }

    function getChildCount()
    {
        return $this->getFinder()->getChildCount($this);
    }

    function getChildren()
    {
        return $this->getFinder()->getChildren($this);
    }

    /**
     * Save the model
     *
     * @param mixed $parent_id optional parent_id this model is being saved under
     * @return int primary key id of created entity
     */
    function save($parent_id = 0, $requestedSaveId = null, $createDefinition = true)
    {
        if ('' == trim($this->getTitle())) {
            throw new Exception('Must have a non blank title to save ' . $this->getType());
        }

        $levelId = $this->findEntityIdByTitle();
        if ($levelId) {
            $this->id = $levelId;
            return $levelId;
        }

        if ($requestedSaveId && $this->requestedIdCorrespondsToExistingRecord($requestedSaveId)) {
            $this->id = $requestedSaveId;
        }

        if ($this->getId()) {
            $sql = sprintf(
                "UPDATE %s SET `title` = %s WHERE id = %d",
                $this->getReadAdapter()->quoteIdentifier($this->getTable()),
                $this->getReadAdapter()->quote($this->getTitle()),
                (int)$this->getId()
            );
            $this->query($sql);
            return $this->id;
        }

        $data = array('title' => $this->getTitle());
        if ($requestedSaveId) {
            $data['id'] = $requestedSaveId;
        }
        $this->getReadAdapter()->insert($this->getTable(), $data);
        $levelId = $this->getReadAdapter()->lastInsertId();
        $this->setId($levelId);
        return $levelId;
    }

    function vehicleFinder()
    {
        return new VF_Vehicle_Finder($this->getSchema());
    }

    function requestedIdCorrespondsToExistingRecord($requestedSaveId)
    {
        $select = $this->getReadAdapter()->select()
            ->from($this->getTable(), 'count(*)')
            ->where('id=?', (int)$requestedSaveId);
        $result = $this->getReadAdapter()->query($select);
        return (bool)$result->fetchColumn();
    }

    function listAll($parent_id = 0)
    {
        return $this->getFinder()->listAll($this, $parent_id);
    }

    function getSortOrder()
    {
        if ($sort = $this->getSchema()->getSorting($this->getType())) {
            return $sort;
        }
        return "ASC";
    }

    function listInUse($parents = array(), $product_id = 0)
    {
        return $this->getFinder()->listInUse($this, $parents, $product_id);
    }

    function listInUseByTitle($parents = array(), $product_id = 0)
    {
        return $this->getFinder()->listInUseByTitle($this, $parents, $product_id);
    }

    function getTable()
    {
        return 'elite_level_' . $this->getSchema()->id() . '_' . str_replace(' ', '_', $this->getType());
    }

    function getLevels()
    {
        return $this->getSchema()->getLevels();
    }

    function getLeafLevel()
    {
        return $this->getSchema()->getLeafLevel();
    }

    function getRootLevel()
    {
        return $this->getSchema()->getRootLevel();
    }

    /** @return integer ID */
    function findEntityIdByTitle($parent_id = 0)
    {
        return $this->getFinder()->findEntityIdByTitle($this->getType(), $this->getTitle(), $parent_id);
    }

    function getSchema()
    {
        return $this->schema;
    }

    function __toString()
    {
        return $this->getTitle();
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