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
use Tests\TestHelpers\Model\Mapper\MockMapper;
use Tests\TestHelpers\Model\Mapper\MockMapper2;
use Tests\TestHelpers\Model\Mapper\MockMapper3;
use Tests\TestHelpers\Traits\AdapterTrait;
use Tests\TestHelpers\Traits\DatabaseCreator;
use Zend\Db\Adapter\Adapter;

class MapperTest extends AbstractTest
{
    use DatabaseCreator;

    public function testCanMapAndLinkObjects()
    {
        $data = array(
            'id' => 1,
            'field1' => 'asdadsad',
            'joinedId' => 2,
            'joinedField1' => 'asdad',
        );

        $mapper = new MockMapper();
        $mapper->attachMapper(new MockMapper2());

        $object = $mapper->populate($data);

        // TODO: split these
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $object);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $object->getTestField2());
        $this->assertEquals('asdadsad', $object->getTestField1());
        $this->assertEquals('asdad', $object->getTestField2()->getTestField1());
    }

    public function testCanMapNestedArrayAndLinkObjects()
    {
        $data = array(
            'id' => 1,
            'field1' => 'asdadsad',
            'entity2' => array(
                'joinedId' => 2,
                'joinedField1' => 'asdad',
            )
        );

        $mapper = new MockMapper();
        $mapper->attachMapper(new MockMapper2());

        // Changing the default map to the desired one
        $mapper->setMap(
            array(
                'id' => 'id',
                'field1' => 'testField1',
                'entity2' => array(
                    'testField2',
                    'Tests\TestHelpers\Model\Mapper\MockMapper2',
                ),
            )
        );

        $object = $mapper->populate($data);

        // TODO: split these
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $object);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $object->getTestField2());
        $this->assertEquals('asdadsad', $object->getTestField1());
        $this->assertEquals('asdad', $object->getTestField2()->getTestField1());

        return $mapper;
    }

    /**
     * @depends testCanMapNestedArrayAndLinkObjects
     *
     * @param MockMapper $mapper
     */
    public function testCanNestMultipleObjects($mapper)
    {
        $data = array(
            'id' => 1,
            'field1' => 'asdadsad',
            'entity2' => array(
                'joinedId' => 2,
                'joinedField1' => 'asdad',
                'entity2' => array(
                    'joinedId' => 3,
                    'joinedField1' => 'asdad12313',
                )
            )
        );

        // Changing the map in the second mapper
        $mapper2 = $mapper->getMapper('Tests\TestHelpers\Model\Mapper\MockMapper2')->setMap(
            array(
                'joinedId' => 'id',
                'joinedField1' => 'testField1',
                'entity2' => array(
                    'testField2',
                    'Tests\TestHelpers\Model\Mapper\MockMapper3',
                ),
            )
        );

        // Attaching the mapper that will process the last map
        $mapper2->attachMapper(new MockMapper3());

        $object = $mapper->populate($data);

        $this->assertEquals('asdad12313', $object->getTestField2()->getTestField2()->getTestField1());
    }

    /**
     * @expectedException \Library\Model\Mapper\Exception\WrongDataTypeException
     */
    public function testDetectsWrongDataType()
    {
        $mapper = new MockMapper();
        $mapper->populate('foo');
    }

    public function testCanMapArraysOfDataAndLinkObjects()
    {
        $data = array(
            'id' => 1,
            'field1' => 'asdadsad',
            'joinedId' => array(
                array(
                    'joinedId' => 2,
                    'joinedField1' => 'asdad',
                ),
                array(
                    'joinedId' => 3,
                    'joinedField1' => 'asdasdwqqasda',
                ),
            )
        );

        $mapper = new MockMapper();
        $mapper->attachMapper(new MockMapper2());
        $mapper->setEntityClass('\Tests\TestHelpers\Model\Entity\ArrayMockEntity');

        $object = $mapper->populate($data);

        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', current($object->getTestField2()));
        $this->assertCount(2, $object->getTestField2());
    }
}
