<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Db;

use Tests\TestHelpers\AbstractTest;
use Tests\TestHelpers\Traits\DatabaseCreator;

class TableGatewayTest extends AbstractTest
{
    use DatabaseCreator;

    public function testGatewayCanReturnPaginator()
    {
        $tableMock = $this->getMockBuilder('\Library\Model\Db\AbstractTableGateway')
            ->setConstructorArgs(array(self::$adapter, 'test'))
            ->getMockForAbstractClass();

        $mock = $this->getMockBuilder('\Library\Model\Mapper\Db\AbstractMapper')
            ->setConstructorArgs(array($tableMock))
            ->getMockForAbstractClass();

        $object = $mock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('id' => 'id', 'field1' => 'testField1'))
            ->fetch()->getPaginator();

        $this->assertInstanceOf('\Zend\Paginator\Paginator', $object);
    }

    public function testGatewayCanReturnResultSet()
    {
        $tableMock = $this->getMockBuilder('\Library\Model\Db\AbstractTableGateway')
            ->setConstructorArgs(array(self::$adapter, 'test'))
            ->getMockForAbstractClass();

        $mock = $this->getMockBuilder('\Library\Model\Mapper\Db\AbstractMapper')
            ->setConstructorArgs(array($tableMock))
            ->getMockForAbstractClass();

        $object = $mock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('id' => 'id', 'field1' => 'testField1'))
            ->fetch()->getResultSet();

        $this->assertInstanceOf('\Zend\Db\ResultSet\ResultSet', $object);
    }
}
