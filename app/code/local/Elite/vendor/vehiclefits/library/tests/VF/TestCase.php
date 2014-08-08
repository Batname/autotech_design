<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class VF_TestCase extends PHPUnit_Framework_TestCase
{
    const ENTITY_TYPE_MAKE = 'make';
    const ENTITY_TYPE_MODEL = 'model';
    const ENTITY_TYPE_YEAR = 'year';

    const ENTITY_TITLE = 'test josh';
    const A_DIFFERENT_ENTITY_TITLE = 'test josh different';
    const NON_EXISTANT_ID = 999;
    const INVALID_TYPE = 'foo';

    protected $maxRunningTime;

    function runTest()
    {
        $startTime = microtime(true);
        parent::runTest();
        $endTime = microtime(true);
        if (0 != $this->maxRunningTime && $endTime - $startTime > $this->maxRunningTime) {
            $this->fail('expected running time: <= ' . $this->maxRunningTime . ' but was ' . ($endTime - $startTime));
        }
    }

    function setMaxRunningTime($maxRunningTime)
    {
        $this->maxRunningTime = $maxRunningTime;
    }

    function setUp()
    {
        VF_Singleton::reset();
        VF_Singleton::getInstance(true);
        VF_Singleton::getInstance()->setRequest(new Zend_Controller_Request_Http);
        $database = new VF_TestDbAdapter(array(
            'dbname' => VAF_DB_NAME,
            'username' => VAF_DB_USERNAME,
            'password' => VAF_DB_PASSWORD
        ));
        VF_Singleton::getInstance()->setReadAdapter($database);

        VF_Schema::$levels = null;

        $_SESSION = array();
        $_GET = array();
        $_REQUEST = array();
        $_POST = array();
        $_FILES = array();

        $this->resetIdentityMaps();
        $this->dropAndRecreateMockProductTable();

        if(class_exists('Mage',false)) {
            Mage::resetRegistry();
        }

        $this->doSetUp();
    }

    function resetIdentityMaps()
    {
        VF_Vehicle_Finder_IdentityMap::reset();
        VF_Vehicle_Finder_IdentityMapByLevel::reset();
        VF_Level_IdentityMap::reset();
        VF_Level_IdentityMap_ByTitle::reset();
        VF_Schema::reset();
        VF_Vehicle_Finder::$IDENTITY_MAP_FINDBYLEVEL = array();
    }

    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    protected function doTearDown()
    {

    }

    function tearDown()
    {
        $this->rollbackTransaction();
        $this->doTearDown();
    }

    protected function switchSchema($levels, $force = false)
    {
        if (!$force) {
            try {
                $schema = new VF_Schema();
                if ($levels == implode(',', $schema->getLevels())) {
                    $this->startTransaction();
                    return;
                }
            } catch (Zend_Db_Statement_Mysqli_Exception $e) {
            } catch (Zend_Db_Statement_Exception $e) {
            }
        }

        $schemaGenerator = new VF_Schema_Generator();
        $schemaGenerator->dropExistingTables();
        $schemaGenerator->execute(explode(',', $levels));

        VF_Schema::reset();

        $this->startTransaction();
    }

    protected function createVehicle($titles = array(), $schema = null)
    {
        $vehicle = VF_Vehicle::create($schema ? $schema : new VF_Schema(), $titles);
        $vehicle->save();
        return $vehicle;
    }

    /** @deprecated use createVehicle() */
    protected function createMMY($makeTitle = 'test make', $modelTitle = 'test model', $yearTitle = 'test year')
    {
        $titles = array(
            'make' => $makeTitle ? $makeTitle : 'make',
            'model' => $modelTitle ? $modelTitle : 'model',
            'year' => $yearTitle ? $yearTitle : 'year'
        );
        $vehicle = VF_Vehicle::create(new VF_Schema(), $titles);
        $vehicle->save();
        return $vehicle;
    }

    function createMMYWithFitment($makeTitle = 'test make', $modelTitle = 'test model', $yearTitle = 'test year')
    {
        $vehicle = $this->createMMY($makeTitle, $modelTitle, $yearTitle);
        $this->insertMappingMMY($vehicle);
        return $vehicle;
    }

    function createTireMMY($make, $model, $year)
    {
        $vehicle = $this->createMMY($make, $model, $year);
        return new VF_Tire_Vehicle($vehicle);
    }

    protected function createYMM($yearTitle = 'test year', $makeTitle = 'test make', $modelTitle = 'test model')
    {
        $vehicle = VF_Vehicle::create(new VF_Schema(), array('year' => $yearTitle, 'make' => $makeTitle, 'model' => $modelTitle));
        $vehicle->save();
        return $vehicle;
    }

    protected function createMMCT($makeTitle = 'test make', $modelTitle = 'test model', $chassisTitle = 'test chassis', $trimTitle = 'test trim')
    {
        $vehicle = VF_Vehicle::create(new VF_Schema(), array('make' => $makeTitle, 'model' => $modelTitle, 'chassis' => $chassisTitle, 'trim' => $trimTitle));
        $vehicle->save();
        return $vehicle;
    }

    protected function createMMTC($makeTitle = 'test make', $modelTitle = 'test model', $trimTitle = 'test trim', $chassisTitle = 'test chassis')
    {
        $vehicle = VF_Vehicle::create(new VF_Schema(), array('make' => $makeTitle, 'model' => $modelTitle, 'trim' => $trimTitle, 'chassis' => $chassisTitle));
        $vehicle->save();
        return $vehicle;
    }

    function assertMMYTitlesEquals($make, $model, $year, $vehicle)
    {
        $expected = array('make' => $make, 'model' => $model, 'year' => $year);
        $this->assertEquals($expected, $vehicle->toTitleArray());
    }

    function assertVehiclesSame($vehicle1, $vehicle2)
    {
        $this->assertEquals($vehicle1->toValueArray(), $vehicle2->toValueArray());
    }

    /**
     * Saves a model, and then looks it back up
     * to help with testing db persistence.
     *
     * @param VF_Level $entity
     * @return VF_Level |null hopefully returns the same model after being saved and reloaded
     *
     * @throws Exception if saving couldn't happen
     */
    protected function saveAndReload(VF_Level $entity, $parent_id = 0)
    {
        $id = $entity->save($parent_id);
        $entity = $this->findEntityById($id, $entity->getType(), $parent_id);
        return $entity;
    }

    protected function findModels(array $ids)
    {
        $return = array();
        foreach ($ids as $id) {
            $return[] = $this->findModelById($id);
        }
        return $return;
    }

    protected function findYears(array $ids)
    {
        $return = array();
        foreach ($ids as $id) {
            $return[] = $this->findYearById($id);
        }
        return $return;
    }

    /** @return VF_Level */
    protected function findMakeById($id)
    {
        return $this->findEntityById($id, self::ENTITY_TYPE_MAKE);
    }

    /** @return VF_Level */
    protected function findModelById($id)
    {
        return $this->findEntityById($id, self::ENTITY_TYPE_MODEL);
    }

    /** @return VF_Level */
    protected function findYearById($id)
    {
        return $this->findEntityById($id, self::ENTITY_TYPE_YEAR);
    }

    /** @return VF_Level */
    protected function findEntityById($id, $level)
    {
        return $this->levelFinder()->find($level, $id);
    }

    /** @return VF_Level */
    protected function findEntityIdByTitle($title, $type)
    {
        $result = $this->query(sprintf(
            "SELECT `id` FROM %s WHERE `title` = %s",
            $this->getReadAdapter()->quoteIdentifier('elite_level_1_' . $type),
            $this->getReadAdapter()->quote($title)
        ));
        $id = $result->fetchColumn(0);
        $result->closeCursor();
        return $id ? $id : false;
    }

    /**
     * Creates a 'make' record for testing and returns it's id
     *
     * @param mixed $title
     * @return int id
     */
    protected function insertMake($title = 'make')
    {
        return $this->insertLevel('make', $title);
    }

    /** @return integer the created fit's ID */
    protected function insertMappingMMY($vehicle, $product_id = 1)
    {
        $mapping = new VF_Mapping($product_id, $vehicle);
        return $mapping->save();
    }

    /** @return integer the created fit's ID */
    protected function insertMappingYMM($year_id, $make_id, $model_id = 0, $product_id = 0)
    {
        $sql = sprintf("REPLACE INTO `elite_1_mapping` ( `year_id`, `make_id`, `model_id`, `entity_id` ) VALUES ( %d, %d, %d, %d )", (int)$year_id, (int)$make_id, (int)$model_id, (int)$product_id);
        $this->query($sql);
        return $this->getReadAdapter()->lastInsertId();
    }

    /** @return integer the created fit's ID */
    protected function insertMappingMMTC($vehicle, $product_id = 1)
    {
        $mapping = new VF_Mapping($product_id, $vehicle);
        return $mapping->save();
    }

    protected function insertUniversalFit($product_id)
    {
        $this->query(sprintf("REPLACE INTO `elite_1_mapping` ( `universal`, `entity_id` ) VALUES ( 1, %d )", (int)$product_id));
        return $this->getReadAdapter()->lastInsertId();
    }

    /**
     * Creates a 'model' record for testing and returns it's id
     *
     * @param integer make_id
     * @param mixed $title
     * @return int id
     */
    protected function insertModel($make_id, $title = '')
    {
        return $this->insertLevel('model', $title, $make_id);
    }

    /**
     * Creates a 'year' record for testing and returns it's id
     *
     * @param integer model_id
     * @param mixed $title
     * @return int id
     */
    protected function insertYear($model_id, $title = '')
    {
        $year = new VF_Level('year');
        $year->setTitle($title);
        return $year->save($model_id);
    }

    protected function insertLevel($level, $title = '', $parent_id = 0, $config = null)
    {
        $id = $this->findEntityIdByTitle($title, $level);
        if ($id) {
            return $id;
        }

        $entity = new VF_Level($level);
        if (!is_null($config)) {
            $entity->setConfig($config);
        }
        $entity->setTitle($title);
        $id = $entity->save($parent_id);
        return $id;
    }

    protected function insertProduct($sku, $table='test_catalog_product_entity', $skuColumn = 'sku')
    {
        $this->query(sprintf("INSERT INTO $table ( `$skuColumn` ) values ( '%s' )", $sku));
        return $this->getReadAdapter()->lastInsertId();
    }

    protected function truncateTable($table)
    {
        try {
            $this->query(sprintf('delete from `%s`', $table));
            $this->query(sprintf('ALTER TABLE `%s` AUTO_INCREMENT = 1`', $table));
        } catch (Exception $e) {
            // ignoring exceptions because the levels might not be MMY
        }
    }

    protected function startTransaction()
    {
        $this->getReadAdapter()->beginTransaction();
    }

    protected function rollbackTransaction()
    {
        $this->getReadAdapter()->rollback();
        if ($this->getReadAdapter()->_transaction_depth !== -1) {
            throw new Exception('invalid transaction nesting');
        }
    }

    function findVehicleByLevelsMMY($make, $model, $year)
    {
        $vehicleFinder = new VF_Vehicle_Finder(new VF_Schema());
        return $vehicleFinder->findOneByLevels(array('make' => $make, 'model' => $model, 'year' => $year));
    }

    function findVehicleByLevelsYMM($year, $make, $model)
    {
        $vehicleFinder = new VF_Vehicle_Finder(new VF_Schema());
        return $vehicleFinder->findOneByLevels(array('make' => $make, 'model' => $model, 'year' => $year));
    }

    function findVehicleByLevelsMMOY($make, $model, $option, $year)
    {
        $vehicleFinder = new VF_Vehicle_Finder(new VF_Schema());
        return $vehicleFinder->findOneByLevels(array('make' => $make, 'model' => $model, 'option' => $option, 'year' => $year));
    }

    protected function findEntityByTitle($type, $title, $parent_id = 0)
    {
        $finder = new VF_Level_Finder();
        return $finder->findEntityByTitle($type, $title, $parent_id);
    }

    /** @return Zend_Db_Statement_Interface */
    protected function query($sql)
    {
        return $this->getReadAdapter()->query($sql);
    }

    /** @return Zend_Db_Adapter_Abstract */
    protected function getReadAdapter()
    {
        $adapter = VF_Singleton::getInstance()->getReadAdapter();
        return $adapter;
    }

    function getRequest($params = array())
    {
        $request = new Zend_Controller_Request_HttpTestCase();
        $request->setParams($params);
        return $request;
    }

    function setRequestParams($requestParams)
    {
        $request = $this->getRequest($requestParams);
        $this->setRequest($request);
    }

    function setRequest($request)
    {
        VF_Singleton::getInstance()->setRequest($request);
    }

    protected function request($controllerName = '', $routeName = '', $uri = false)
    {
        if ($uri) {
            $request = $this->getMock('Mage_Core_Controller_Request_Http', array('getControllerName', 'getRouteName'), array($uri), '', true, false);
        } else {
            $request = $this->getMock('Mage_Core_Controller_Request_Http', array('getControllerName', 'getRouteName'), array(), '', false, false);
        }
        $request->expects($this->any())->method('getControllerName')->will($this->returnValue($controllerName));
        $request->expects($this->any())->method('getRouteName')->will($this->returnValue($routeName));
        return $request;
    }

    function newProduct($id = null)
    {
        $product = new Elite_Vaf_Model_Catalog_Product;
        if (!is_null($id)) {
            $product->setId($id);
        }
        return $product;
    }

    function newVFProduct($id = null)
    {
        $product = new VF_Product;
        if (!is_null($id)) {
            $product->setId($id);
        }
        return $product;
    }

    function newWheelProduct($id = null)
    {
        $product = new VF_Wheel_Catalog_Product($this->newVFProduct($id));
        return $product;
    }

    function newWheelAdapterProduct($id = null)
    {
        $product = new VF_Wheeladapter_Catalog_Product($this->newVFProduct($id));
        return $product;
    }

    function newTireProduct($id = null, $tireSize = null, $tireType = null)
    {
        $tireProduct = new VF_Tire_Catalog_TireProduct($this->newVFProduct($id));
        if (!is_null($tireSize)) {
            $tireProduct->setTireSize($tireSize);
        }
        if (!is_null($tireType)) {
            $tireProduct->setTireType($tireType);
        }
        return $tireProduct;
    }

    /** @return VF_Tire_FlexibleSearch */
    function flexibleTireSearch($requestParams = array())
    {
        $this->setRequestParams($requestParams);

        $flexibleSearch = new VF_FlexibleSearch(new VF_Schema(), $this->getRequest($requestParams));
        $tireFlexibleSearch = new VF_Tire_FlexibleSearch($flexibleSearch);
        return $tireFlexibleSearch;
    }

    /** @return VF_Wheeladapter_FlexibleSearch */
    function flexibleWheeladapterSearch($requestParams = array())
    {
        $this->setRequestParams($requestParams);

        $flexibleSearch = new VF_FlexibleSearch(new VF_Schema(), $this->getRequest($requestParams));
        $tireFlexibleSearch = new VF_Wheeladapter_FlexibleSearch($flexibleSearch);
        return $tireFlexibleSearch;
    }

    function flexibleWheelSearch($requestParams = array())
    {
        if (count($requestParams)) {
            $this->setRequestParams($requestParams);
            $request = $this->getRequest($requestParams);
        } else {
            $request = VF_Singleton::getInstance()->getRequest();
        }

        $flexibleSearch = new VF_FlexibleSearch(new VF_Schema(), $request);
        $flexibleSearch = new VF_Wheel_FlexibleSearch($flexibleSearch);
        return $flexibleSearch;
    }

    protected function dropAndRecreateMockProductTable()
    {
        try {
            $this->query("DROP TABLE `test_catalog_product_entity`");
        } catch (Exception $e) {

        }

        $this->query("CREATE TABLE IF NOT EXISTS `test_catalog_product_entity` (
          `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `entity_type_id` smallint(8) unsigned NOT NULL DEFAULT '0',
          `attribute_set_id` smallint(5) unsigned NOT NULL DEFAULT '0',
          `type_id` varchar(32) NOT NULL DEFAULT 'simple',
          `sku` varchar(64) DEFAULT NULL,
          `category_ids` varchar(64),
          `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          `has_options` smallint(1) NOT NULL DEFAULT '0',
          `required_options` tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`entity_id`),
          KEY `FK_CATALOG_PRODUCT_ENTITY_ENTITY_TYPE` (`entity_type_id`),
          KEY `FK_CATALOG_PRODUCT_ENTITY_ATTRIBUTE_SET_ID` (`attribute_set_id`),
          KEY `sku` (`sku`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product Entities' AUTO_INCREMENT=1 ;");

        try {
            $this->query("DROP TABLE `ps_product`");
        } catch (Exception $e) {

        }

        $this->query("CREATE TABLE `ps_product` (
          `id_product` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `reference` varchar(32) NOT NULL DEFAULT 'simple',
          PRIMARY KEY (`id_product`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product Entities' AUTO_INCREMENT=1 ;");

    }

    function getProductForSku($sku)
    {
        $sql = sprintf(
            "SELECT `entity_id` from `test_catalog_product_entity` WHERE `sku` = %s",
            $this->getReadAdapter()->quote($sku)
        );
        $r = $this->query($sql);
        $product_id = $r->fetchColumn();
        $r->closeCursor();

        $product = new Elite_Vaf_Model_Catalog_Product();
        $product->setId($product_id);

        return $product;
    }

    function getVFProductForSku($sku)
    {
        $sql = sprintf(
            "SELECT `entity_id` from `test_catalog_product_entity` WHERE `sku` = %s",
            $this->getReadAdapter()->quote($sku)
        );
        $r = $this->query($sql);
        $product_id = $r->fetchColumn();
        $r->closeCursor();

        $product = new VF_Product();
        $product->setId($product_id);

        return $product;
    }

    function levelFinder()
    {
        return new VF_Level_Finder;
    }

    function vehicleFinder($schema = null)
    {
        return new VF_Vehicle_Finder($schema ? $schema : new VF_Schema());
    }

    function boltPattern($boltPatternString, $offset = null)
    {
        return VF_Wheel_BoltPattern::create($boltPatternString, $offset);
    }

    function wheelAdapterFinder()
    {
        return new VF_Wheeladapter_Finder;
    }

    function tireFinder()
    {
        return new VF_Tire_Finder;
    }

    function noteFinder()
    {
        return new VF_Note_Finder();
    }

    function definitionsController($request = null)
    {
        if (is_null($request)) {
            $request = new Zend_Controller_Request_Http();
        }
        require_once(ELITE_PATH . '/Vaf/controllers/Admin/VehicleslistController.php');
        require_once(ELITE_PATH . '/Vaf/controllers/Admin/DefinitionsController/TestSubClass.php');
        $controller = new Elite_Vaf_Admin_DefinitionsController_TestSubClass($request, new Zend_Controller_Response_Http());
        return $controller;
    }

    function vehicleExists($titles = array(), $allowPartialVehicleMatch = false, $schema = null)
    {
        return 0 != count($this->vehicleFinder($schema)->findByLevels($titles, $allowPartialVehicleMatch));
    }

    function createNoteDefinition($code, $message)
    {
        $this->noteFinder()->insert($code, $message);
    }

    function newMake($title)
    {
        $make = new VF_Level('make');
        $make->setTitle($title);
        return $make;
    }

    function newModel($title)
    {
        $model = new VF_Level('model');
        $model->setTitle($title);
        return $model;
    }

    function newYear($title)
    {
        $year = new VF_Level('year');
        $year->setTitle($title);
        return $year;
    }

    function newLevel($level, $title)
    {
        $level = new VF_Level($level);
        $level->setTitle($title);
        return $level;
    }

    function schemaGenerator()
    {
        return new VF_Schema_Generator();
    }

    function newNoteProduct($id = 0)
    {
        $product = $this->newVFProduct($id);
        return new VF_Note_Catalog_Product($product);
    }

    function merge($slaveLevels, $masterLevel)
    {
        $merge = new Elite_Vaf_Model_Merge($slaveLevels, $masterLevel);
        $merge->execute();
    }

    function split($vehicle, $grain, $newTitles)
    {
        $split = new Elite_Vaf_Model_Split($vehicle, $grain, $newTitles);
        $split->execute();
    }

    function getHelper($config = array(), $requestParams = array())
    {
        $request = $this->getRequest($requestParams);
        $helper = VF_Singleton::getInstance();
        $helper->reset();
        $helper->setRequest($request);
        if (count($config)) {
            $helper->setConfig(new Zend_Config($config, true));
        }
        return $helper;
    }

}

class Elite_Vaf_Model_TestSubClass extends VF_Level
{

    function getLevels()
    {
        return array('make', 'model', 'year');
    }

    function getNextLevel()
    {
        return '';
    }

    function getPrevLevel()
    {
        return '';
    }

    function getLeafLevel()
    {
        return 'year';
    }

    function createEntity($level, $id = 0)
    {
        switch ($level) {
            case 'make':
                return new Elite_Vaf_Model_TestSubClass_Make($level, $id);
                break;
            case 'model':
                return new Elite_Vaf_Model_TestSubClass_Model($level, $id);
                break;
            case 'year':
                return new Elite_Vaf_Model_TestSubClass_Year($level, $id);
                break;
        }
        return new VF_Level($level, $id);
    }

}