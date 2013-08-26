<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Model\Mapper;

use Tests\TestHelpers\AbstractTest;
use Tests\TestHelpers\Model\Entity\MockEntity;
use Tests\TestHelpers\Traits\AdapterTrait;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

class MapperTest extends AbstractTest
{
    /**
     * @var string
     */
    protected static $sqlitePaths = 'module/Tests/database';

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected static $adapter;

    public static function setUpBeforeClass()
    {
        // Setting up the adapter
        self::$adapter = new Adapter(array(
            'driver' => 'Pdo_Sqlite',
            'database' => self::$sqlitePaths . '/database_mapper.db'
        ));

        self::$adapter->query(file_get_contents(self::$sqlitePaths . '/schema.sqlite.sql'), Adapter::QUERY_MODE_EXECUTE);
        self::$adapter->query(file_get_contents(self::$sqlitePaths . '/data.sqlite.sql'), Adapter::QUERY_MODE_EXECUTE);
    }

    public function testCanMapDataToObject()
    {
        $mock = $this->getMockBuilder('\Library\Model\Mapper\AbstractDbMapper')
            ->setConstructorArgs(array(new TableGateway('test', self::$adapter)))
            ->getMockForAbstractClass();

        $entityObject = $mock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('id' => 'id', 'field1' => 'testField1'))
            ->findById(1);

        $this->assertEquals(1, $entityObject->getId());

        return $entityObject;
    }

    /**
     * @depends testCanMapDataToObject
     *
     * @param MockEntity $object
     */
    public function testCanMapDataToOtherFields(MockEntity $object)
    {
        $this->assertEquals('test1', $object->getTestField1());
    }

    public static function tearDownAfterClass()
    {
        // Removing the test database (connection to db needs to be closed)
        self::$adapter->getDriver()->getConnection()->disconnect();
        unlink(self::$sqlitePaths . '/database_mapper.db');
    }
}
