<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model\Mapper;

use Library\Model\Entity\EntityInterface;
use Library\Model\Mapper\Db\AbstractMapper as DbAbstractMapper;

class MockMapper extends DbAbstractMapper
{
    /**
     * Class name of the entity that the data will be mapped to
     *
     * @var string
     */
    protected $entityClass = '\Tests\TestHelpers\Model\Entity\MockEntity';

    /**
     * The map that will be used to populate the object
     *
     * @var array
     */
    protected $map = array(
        'id' => 'id',
        'field1' => 'testField1',
        'field2' => 'testField2',
    );

    /**
     * An array that indicates the links between the data sources
     *
     * @var array
     */
    protected $dataSourceLinks = array(
        'test' => array(
            'table' => 'test_join',
            'on' => array(
                'id' => 'testId',
            )
        )
    );

    /**
     * @param EntityInterface $object
     * @return mixed
     */
    public function save(EntityInterface $object)
    {
        // TODO: Implement save() method.
    }
}
