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
use Zend\EventManager\Event;

class TableGatewayTest extends AbstractTest
{
    use DatabaseCreator;

    public function testCanFindByIdWithoutAMapper()
    {
        /** @var $tableMock \Library\Model\Db\AbstractTableGateway */
        $tableMock = $this->getMockBuilder('\Library\Model\Db\AbstractTableGateway')
            ->setConstructorArgs(array(self::$adapter, 'test'))
            ->getMockForAbstractClass();

        $entityObject = $tableMock->findById(2);

        $this->assertEquals(2, $entityObject['id']);
    }

    public function testGatewayCanReturnPaginator()
    {
        /** @var $tableMock \Library\Model\Db\AbstractTableGateway */
        $tableMock = $this->getMockBuilder('\Library\Model\Db\AbstractTableGateway')
            ->setConstructorArgs(array(self::$adapter, 'test'))
            ->getMockForAbstractClass();

        /** @var $mapperMock \Library\Model\Mapper\Db\AbstractMapper */
        $mapperMock = $this->getMockBuilder('\Library\Model\Mapper\Db\AbstractMapper')
            ->setConstructorArgs(array($tableMock))
            ->getMockForAbstractClass();

        // Updating the map in the mapper
        $mapperMock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('test' => array('id' => 'id', 'field1' => 'testField1')));

        /** @var $object \Library\Model\Db\ResultProcessor */
        $object = $tableMock->fetch();

        // Changing the map in the paginator
        $object->getEventManager()->attach(
            'changePaginatorMap',
            function (Event $e) {
                $params = $e->getParams();
                $map    = & $params['map'];
                $map    = 'test';

                return $map;
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

    public function testGatewayCanReturnResultSet()
    {
        /** @var $tableMock \Library\Model\Db\AbstractTableGateway */
        $tableMock = $this->getMockBuilder('\Library\Model\Db\AbstractTableGateway')
            ->setConstructorArgs(array(self::$adapter, 'test'))
            ->getMockForAbstractClass();

        /** @var $mapperMock \Library\Model\Mapper\Db\AbstractMapper */
        $mapperMock = $this->getMockBuilder('\Library\Model\Mapper\Db\AbstractMapper')
            ->setConstructorArgs(array($tableMock))
            ->getMockForAbstractClass();

        // Updating the map in the mapper
        $mapperMock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('id' => 'id', 'field1' => 'testField1'));

        /** @var $object \Zend\Db\ResultSet\ResultSet */
        $object = $tableMock->fetch()->getResultSet();

        $this->assertInstanceOf('\Zend\Db\ResultSet\ResultSet', $object);
        $this->assertEquals(2, $object->count());
    }
}
