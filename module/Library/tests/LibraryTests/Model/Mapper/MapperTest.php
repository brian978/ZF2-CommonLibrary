<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Model\Mapper;

use Tests\TestHelpers\AbstractTest;
use Tests\TestHelpers\Model\Entity\MappedMockEntity;
use Tests\TestHelpers\Model\Mapper\DefaultMockMapper;
use Tests\TestHelpers\Model\Mapper\MockMapper;
use Tests\TestHelpers\Model\Mapper\MockMapper2;
use Tests\TestHelpers\Model\Mapper\MockMapper3;
use Tests\TestHelpers\Traits\AdapterTrait;

class MapperTest extends AbstractTest
{
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

        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object->getTestField2());
        $this->assertEquals('asdadsad', $object->getTestField1());
        $this->assertEquals('asdad', $object->getTestField2()->getTestField1());
    }

    public function testCanMapAndLinkObjectsWithoutAMapperEntity()
    {
        $data = array(
            'id' => 1,
            'field1' => 'asdadsad',
            'joinedId' => 2,
            'joinedField1' => 'asdad',
        );

        $mapper = new MockMapper();
        $mapper->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity');
        $mapper->attachMapper(new MockMapper2());

        $object = $mapper->populate($data);

        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MockEntity', $object);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object->getTestField2());
        $this->assertEquals('asdadsad', $object->getTestField1());
        $this->assertEquals('asdad', $object->getTestField2()->getTestField1());
    }

    public function testCanMapUsingAMapName()
    {
        $data = array(
            'id' => 1,
            'field1' => 'asdadsad',
            'joinedId' => 2,
            'joinedField1' => 'asdad',
        );

        $mapper = new DefaultMockMapper();
        $mapper->attachMapper(new MockMapper2());

        $object = $mapper->populate($data, 'default');

        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object->getTestField2());
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

        $mapper  = new MockMapper();
        $mapper2 = new MockMapper2();
        $mapper->attachMapper($mapper2);

        // Changing the default map to the desired one
        $mapper->setMap(
            array(
                'default' => array(
                    'id' => 'id',
                    'field1' => 'testField1',
                    'entity2' => array(
                        'mapper' => array(
                            'testField2',
                            'Tests\TestHelpers\Model\Mapper\MockMapper2',
                        )
                    ),
                )
            )
        );

        $object = $mapper->populate($data);

        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object->getTestField2());
        $this->assertEquals('asdadsad', $object->getTestField1());
        $this->assertEquals('asdad', $object->getTestField2()->getTestField1());

        return array(array($mapper, $mapper2));
    }

    /**
     * @depends      testCanMapNestedArrayAndLinkObjects
     * @dataProvider testCanMapNestedArrayAndLinkObjects
     *
     * @param MockMapper $mapper
     * @param MockMapper $mapper2
     */
    public function testCanNestMultipleObjects($mapper, $mapper2)
    {
        $data = array(
            'id' => 1,
            'field1' => 'asdadsad',
            'entity2' => array(
                'joinedId' => 2,
                'joinedField1' => 'asdad',
                'entity2' => array(
                    'joinedId' => 3,
                    'joinedField' => '777712313',
                )
            )
        );

        // Changing the map in the second mapper
        $mapper2->setMap(
            array(
                'default' => array(
                    'joinedId' => 'id',
                    'joinedField1' => 'testField1',
                    'entity2' => array(
                        'mapper' => array(
                            'testField2',
                            'Tests\TestHelpers\Model\Mapper\MockMapper3',
                        )
                    ),
                )
            )
        );

        // Attaching the mapper that will process the last map
        $mapper2->attachMapper(new MockMapper3());

        /** @var $object MappedMockEntity */
        $object = $mapper->populate($data);

        $this->assertEquals('777712313', $object->getTestField2()->getTestField2()->getTestField1());
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

        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', current($object->getTestField2()));
        $this->assertCount(2, $object->getTestField2());
    }

    public function testCanGetBaseMapper()
    {
        $mapper  = new MockMapper();
        $mapper2 = new MockMapper2();
        $mapper3 = new MockMapper3();

        $mapper->attachMapper($mapper2);
        $mapper2->attachMapper($mapper3);

        $this->assertEquals($mapper, $mapper2->getBaseMapper());
    }

    public function testCanReturnSelfAsBaseMapper()
    {
        $mapper  = new MockMapper();
        $mapper2 = new MockMapper2();

        $mapper->attachMapper($mapper2);

        $this->assertEquals($mapper, $mapper->getBaseMapper());
    }

    public function testCanGetBaseMapperAfterAttachedToAnotherMapper()
    {
        $mapper  = new MockMapper();
        $mapper2 = new MockMapper2();
        $mapper3 = new MockMapper3();

        $mapper2->attachMapper($mapper3);

        // Getting the base mapper for $mapper3 just so it records mapper2 as a base
        // This is used to test if it can handle the base mapper being attached to another mapper
        $mapper3->getBaseMapper();

        // Now we attach $mapper3's base to another mapper to see if $mapper3 reacts and reconfigures the base
        $mapper->attachMapper($mapper2);

        $this->assertEquals($mapper, $mapper3->getBaseMapper());
    }

    public function testMapperCanExtractData()
    {
        $mapper = (new MockMapper())->attachMapper(new MockMapper2());
        $data   = array(
            'id' => 1,
            'field1' => 'test',
            'joinedId' => 2,
            'joinedField1' => 'test_joined'
        );

        $object    = $mapper->populate($data);
        $extracted = $mapper->extract($object);

        $this->assertEquals($data, $extracted);
    }

    public function testCanMapNestedArrayAndLinkObjectsWithDifferentMaps()
    {
        $data = array(
            'id' => 1,
            'field1' => 'asdadsad',
            'entity2' => array(
                'joinedId' => 2,
                'joinedField1' => 'asdad',
            )
        );

        $mapper  = new MockMapper();
        $mapper2 = new MockMapper2();
        $mapper->attachMapper($mapper2);

        // Changing the default map to the desired one
        $mapper->setMap(
            array(
                'default' => array(
                    'id' => 'id',
                    'field1' => 'testField1',
                    'entity2' => array(
                        'mapper' => array(
                            'testField2',
                            'Tests\TestHelpers\Model\Mapper\MockMapper2',
                            'customMap'
                        )
                    )
                )
            )
        );

        $mapper2->setMap(
            array(
                'customMap' => array(
                    'joinedId' => 'id',
                    'joinedField1' => 'testField1',
                )
            )
        );

        $object = $mapper->populate($data);

        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object);
        $this->assertInstanceOf('\Tests\TestHelpers\Model\Entity\MappedMockEntity', $object->getTestField2());
        $this->assertEquals('asdadsad', $object->getTestField1());
        $this->assertEquals('asdad', $object->getTestField2()->getTestField1());

        return array(array($mapper, $mapper2));
    }
}
