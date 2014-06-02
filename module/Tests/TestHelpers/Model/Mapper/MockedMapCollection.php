<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model\Mapper;

use Library\Model\Mapper\MapCollection;

class MockedMapCollection extends MapCollection
{
    /**
     * An array representing the data in the collection
     *
     * @var array
     */
    protected $collection = array(
        'default' => array(
            'entity' => '\Tests\TestHelpers\Model\Entity\MappedMockEntity',
            'specs' => array(
                'id' => 'id',
                'field1' => 'testField1',
                'joinedId' => array(
                    'toProperty' => 'testField2',
                    'map' => 'defaultJoin'
                ),
            )
        ),
        'defaultJoin' => array(
            'entity' => '\Tests\TestHelpers\Model\Entity\MappedMockEntity',
            'specs' => array(
                'joinedId' => 'id',
                'joinedField1' => 'testField1',
                'joinedField2' => 'testField2',
            )
        ),

        'collectionDefault' => array(
            'entity' => '\Tests\TestHelpers\Model\Entity\CEntity1',
            'identProperty' => 'id',
            'specs' => array(
                'id' => 'id',
                'name' => 'name',
                'childId' => array(
                    'toProperty' => 'cEntity2',
                    'map' => 'collectionJoinCEntity2'
                ),
            )
        ),

        'collectionJoinCEntity2' => array(
            'entity' => '\Tests\TestHelpers\Model\Entity\CEntity2',
            'identProperty' => 'id',
            'specs' => array(
                'childId' => 'id',
                'childName' => 'name',
                'childTypeId' => 'typeId',
                'childId2' => array(
                    'toProperty' => 'cEntity3',
                    'map' => 'collectionJoinCEntity3'
                ),
            )
        ),

        'collectionJoinCEntity3' => array(
            'entity' => '\Tests\TestHelpers\Model\Entity\CEntity3',
            'identProperty' => 'id',
            'specs' => array(
                'childId2' => 'id',
                'childName2' => 'name'
            )
        )
    );
}
