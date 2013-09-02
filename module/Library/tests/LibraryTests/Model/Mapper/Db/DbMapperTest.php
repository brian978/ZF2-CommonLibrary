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
use Tests\TestHelpers\Traits\DatabaseCreator;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

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

    public function testSelectFromDatabase()
    {
        $table = new TableGateway('test', self::$adapter);

        $this->assertEquals(1, $table->select()->count());
    }
}
