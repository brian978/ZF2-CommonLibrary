<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Model\Db;

use Library\Collection\GatewayTracker;
use Library\Model\Db\ResultProcessorInterface;
use Library\Model\Db\TableGateway;
use Tests\TestHelpers\AbstractTest;
use Tests\TestHelpers\Traits\DatabaseCreator;
use Zend\EventManager\Event;
use Tests\TestHelpers\Db\TableGateway as TestTableGateway;

class TableGatewayTest extends AbstractTest
{
    use DatabaseCreator;

    /**
     * @param $dir
     */
    protected function _removeRecursive($dir)
    {
        if (file_exists($dir)) {
            $dirIt = new \DirectoryIterator($dir);

            /** @var $entry \SplFileInfo */
            foreach ($dirIt as $entry) {
                $filename = $entry->getFilename();
                if ($filename == '.' || $filename == '..') {
                    continue;
                }

                if ($entry->isFile()) {
                    unlink($entry->getPathname());
                } else {
                    $this->_removeRecursive($entry->getPathname());
                }
            }

            rmdir($dir);
        }
    }

    public function testCanFindByIdWithoutAMapper()
    {
        $table        = new TableGateway(self::$adapter, 'test');
        $entityObject = $table->findById(2);

        $this->assertEquals(2, $entityObject['id']);

        return $table;
    }

    /**
     * @depends testCanFindByIdWithoutAMapper
     *
     * @param \Library\Model\Db\TableGateway $tableMock
     */
    public function testGatewayCanUseAttachedMapper($tableMock)
    {
        /** @var $mapperMock \Library\Model\Mapper\AbstractMapper */
        $mapperMock = $this->getMockBuilder('\Library\Model\Mapper\AbstractMapper')
            ->getMockForAbstractClass();

        // Updating the map in the mapper
        $mapperMock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('default' => array('id' => 'id', 'field1' => 'testField1')));

        $tableMock->setMapper($mapperMock);

        $entityObject = $tableMock->findById(2);

        $this->assertEquals(2, $entityObject->getId());
    }

    public function testGatewayCanReturnPaginator()
    {
        $tableMock = new TableGateway(self::$adapter, 'test');

        /** @var $mapperMock \Library\Model\Mapper\AbstractMapper */
        $mapperMock = $this->getMockBuilder('\Library\Model\Mapper\AbstractMapper')
            ->getMockForAbstractClass();

        // Updating the map in the mapper
        $mapperMock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('test' => array('id' => 'id', 'field1' => 'testField1')));

        $tableMock->setMapper($mapperMock);

        /** @var $object \Library\Model\Db\ResultProcessor */
        $object = $tableMock->fetch();

        // Changing the map in the paginator
        $object->getEventManager()->attach(
            ResultProcessorInterface::EVENT_CHANGE_MAP,
            function (Event $e) use ($object) {
                if ($e->getTarget() === $object) {
                    $e->getParam(0)->setName('test');
                }
            }
        );

        $paginator = $object->getPaginator()
            ->setItemCountPerPage(1)
            ->setCurrentPageNumber(1);

        /** @var $currentItems \Zend\Db\ResultSet\ResultSet */
        $currentItems = $paginator->getCurrentItems();
        $currentItem  = $currentItems->current();

        $this->assertInstanceOf('\Zend\Paginator\Paginator', $paginator);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $currentItem);
        $this->assertNotEquals(0, $currentItem->getId());
        $this->assertEquals(1, $currentItems->count());
    }

    public function testTableGatewayReturnsJoinedResult()
    {
        $table = new TestTableGateway(self::$adapter, 'test');

        /** @var $object \Library\Model\Db\ResultProcessor */
        $object    = $table->fetchJoined();
        $resultSet = $object->getResultSet();

        $this->assertInstanceOf('\Zend\Db\ResultSet\ResultSet', $resultSet);
        $this->assertEquals(1, $resultSet->count());
    }

    public function testTableGatewayReturnsJoinedMappedResult()
    {
        /**
         * ------------------------------------
         * CREATING THE MOCKS FOR THE MAPPERS
         * ------------------------------------
         */
        /** @var $mapperMock \Library\Model\Mapper\AbstractMapper */
        $mapperMock = $this->getMockBuilder('\Library\Model\Mapper\AbstractMapper')
            ->getMockForAbstractClass();

        // Updating the map in the mapper
        $mapperMock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity');

        // Creating a clone for the mapper to set up 2 different mappers
        // and to be able to attach it to the first mapper
        $mapperMock2 = clone $mapperMock;

        $mapperMock->attachMapper($mapperMock2);
        $mapperMock->setMap(
            array(
                'id' => 'id',
                'field1' => 'testField1',
                'joinedId' => array(
                    'mapper' => array(
                        'testField2', // Field where to put the result
                        get_class($mapperMock2),
                    )
                )
            )
        );

        $mapperMock2->setMap(
            array(
                'id' => 'id',
                'field1' => 'testField1',
                'field2' => 'testField2',
            )
        );

        $table  = new TestTableGateway(self::$adapter, 'test');
        $table2 = new TestTableGateway(self::$adapter, 'test2');

        $table->setMapper($mapperMock);
        $table2->setMapper($mapperMock2);

        /** @var $object \Library\Model\Db\ResultProcessor */
        $object    = $table->fetchJoined();
        $resultSet = $object->getResultSet();

        /** @var $currentResult \Tests\TestHelpers\Model\Entity\MockEntity */
        $currentResult = $resultSet->current();

        $this->assertInstanceOf('\Zend\Db\ResultSet\ResultSet', $resultSet);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $currentResult);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $currentResult->getTestField2());
    }

    public function testCanReturnJoinedMapperResultUsingTracketGateways()
    {
        /**
         * ------------------------------------
         * CREATING THE MOCKS FOR THE MAPPERS
         * ------------------------------------
         */
        /** @var $mapperMock \Library\Model\Mapper\AbstractMapper */
        $mapperMock = $this->getMockBuilder('\Library\Model\Mapper\AbstractMapper')
            ->getMockForAbstractClass();

        // Updating the map in the mapper
        $mapperMock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity');

        // Creating a clone for the mapper to set up 2 different mappers
        // and to be able to attach it to the first mapper
        $mapperMock2 = clone $mapperMock;

        $mapperMock->setMap(
            array(
                'id' => 'id',
                'field1' => 'testField1',
                'joinedId' => array(
                    'mapper' => array(
                        'testField2', // Field where to put the result
                        get_class($mapperMock2),
                    )
                )
            )
        );

        $mapperMock2->setMap(
            array(
                'id' => 'id',
                'field1' => 'testField1',
                'field2' => 'testField2',
            )
        );

        $table  = new TestTableGateway(self::$adapter, 'test');
        $table2 = new TestTableGateway(self::$adapter, 'test2');

        $table->setMapper($mapperMock);
        $table2->setMapper($mapperMock2);

        // Tracking the mappers
        $tracker = new GatewayTracker();
        $tracker->track($table)
            ->track($table2);

        /** @var $object \Library\Model\Db\ResultProcessor */
        $object    = $table->fetchJoined();
        $resultSet = $object->getResultSet();

        /** @var $currentResult \Tests\TestHelpers\Model\Entity\MockEntity */
        $currentResult = $resultSet->current();

        $this->assertInstanceOf('\Zend\Db\ResultSet\ResultSet', $resultSet);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $currentResult);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $currentResult->getTestField2());
    }

    /**
     * @expectedException \PHPUnit_Framework_SkippedTestError
     */
    public function testGatewayCanReturnResultSetAndCacheResult()
    {
        $this->markTestSkipped('Cache must be redone');

        // Cleaning up the files first
        if (is_dir('module/Tests/caches')) {
            $this->_removeRecursive('module/Tests/caches');
        }

        mkdir('module/Tests/caches');

        $cacheHit = false;

        /** @var $cache \Zend\Cache\Pattern\ObjectCache */
        $cache = $this->serviceManager->get('Zend\Cache');
        $table = new TableGateway(self::$adapter, 'test');
        $table->setCache($cache);

        /** @var $mapperMock \Library\Model\Mapper\AbstractMapper */
        $mapperMock = $this->getMockBuilder('\Library\Model\Mapper\AbstractMapper')
            ->getMockForAbstractClass();

        // Updating the map in the mapper
        $mapperMock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('id' => 'id', 'field1' => 'testField1'));

        // An event to track the cache hit
        $table->getCache()
            ->getOptions()
            ->getStorage()
            ->getEventManager()
            ->attach(
                'getItem.post',
                function () use (&$cacheHit) {
                    $cacheHit = true;
                }
            );

        // Populating the cache
        $table->cache()->fetch()->getResultSet();

        // Triggering the cache hit
        $resultSet = $table->cache()->fetch()->getResultSet();

        $this->assertInstanceOf('\Zend\Db\ResultSet\ResultSet', $resultSet);
        $this->assertTrue($cacheHit);
    }

    /**
     * @expectedException \PHPUnit_Framework_SkippedTestError
     */
    public function testGatewayCanReturnCachedResultSet()
    {
        $this->markTestSkipped('Cache must be redone');

        /** @var $cache \Zend\Cache\Pattern\ObjectCache */
        $cache = $this->serviceManager->get('Zend\Cache');
        $table = new TableGateway(self::$adapter, 'test');
        $table->setCache($cache);

        /** @var $mapperMock \Library\Model\Mapper\AbstractMapper */
        $mapperMock = $this->getMockBuilder('\Library\Model\Mapper\AbstractMapper')
            ->getMockForAbstractClass();

        // Updating the map in the mapper
        $mapperMock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('id' => 'id', 'field1' => 'testField1'));

        /** @var $object \Zend\Db\ResultSet\ResultSet */
        $object = $table->fetch()->cache()->getResultSet();

        $this->assertInstanceOf('\Zend\Db\ResultSet\ResultSet', $object);
        $this->assertEquals(2, $object->count());

        $this->_removeRecursive('module/Tests/caches');
    }
}
