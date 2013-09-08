<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Model\Mapper\Db;

use Tests\TestHelpers\AbstractTest;
use Tests\TestHelpers\Model\Entity\MockEntity;
use Tests\TestHelpers\Model\Mapper\Db\DbMockMapper;
use Tests\TestHelpers\Model\Mapper\Db\DbMockMapper2;
use Tests\TestHelpers\Traits\DatabaseCreator;
use Zend\Db\Adapter\Adapter;

class DbMapperTest extends AbstractTest
{
    use DatabaseCreator;

    public function testCanMapDataToObject()
    {
        $tableMock = $this->getMockBuilder('\Library\Model\Db\AbstractTableGateway')
            ->setConstructorArgs(array('test', self::$adapter))
            ->getMockForAbstractClass();

        $mock = $this->getMockBuilder('\Library\Model\Mapper\Db\AbstractMapper')
            ->setConstructorArgs(array($tableMock))
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

    public function testCanJoinTablesAndMapObjects()
    {
        $testTableMock = $this->getMockBuilder('\Library\Model\Db\AbstractTableGateway')
            ->setConstructorArgs(array('test', self::$adapter))
            ->getMockForAbstractClass();

        $testJoinMock = $this->getMockBuilder('\Library\Model\Db\AbstractTableGateway')
            ->setConstructorArgs(array('test_join', self::$adapter))
            ->getMockForAbstractClass();

        $baseMapper = new DbMockMapper($testTableMock);
        $baseMapper->attachMapper(new DbMockMapper2($testJoinMock));

        /** @var $object MockEntity */
        $object = $baseMapper->findById(1);

        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $object);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $object->getTestField2());
    }
}
