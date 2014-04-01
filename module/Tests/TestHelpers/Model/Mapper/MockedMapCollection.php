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
        )
    );
}
