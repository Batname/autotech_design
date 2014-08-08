<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SingletonTest_DatabaseTest extends VF_TestCase
{
    /**
     * Should throw exception if trying to get database before one is set
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGetDatabaseBeforeOneIsSet()
    {
        $singleton = new VF_Singleton();
        $singleton->getReadAdapter();
    }

    function testShouldReturnInjectedDatabase()
    {
        $database = new VF_TestDbAdapter(array(
            'dbname' => VAF_DB_NAME,
            'username' => VAF_DB_USERNAME,
            'password' => VAF_DB_PASSWORD
        ));

        $singleton = new VF_Singleton();
        $singleton->setReadAdapter($database);
        $this->assertSame($database, $singleton->getReadAdapter(), 'should return injected database');
    }
}