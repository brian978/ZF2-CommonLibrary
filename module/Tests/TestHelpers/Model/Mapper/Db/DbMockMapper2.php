<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model\Mapper\Db;

use Library\Model\Entity\EntityInterface;
use Library\Model\Mapper\Db\AbstractMapper;
use Zend\Db\Sql\Select;

class DbMockMapper2 extends AbstractMapper
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
        'testId' => 'id',
        'testField1' => 'testField1',
        'testNewId' => array(
            'mapper' => array(
                'testField2',
                'Tests\TestHelpers\Model\Mapper\Db\DbMockMapper3',
            ),
            'dataSource' => array(
                'table' => array('testNewJoin' => 'test'),
                'type' => Select::JOIN_INNER,
                'on' => array(
                    'id' => 'id',
                ),
                'columns' => array(
                    'testNewId' => 'id',
                    'testNewField1' => 'field1',
                    'testNewField2' => 'field2',
                )
            )
        ),
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
