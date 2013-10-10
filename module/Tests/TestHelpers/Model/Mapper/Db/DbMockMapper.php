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

class DbMockMapper extends AbstractMapper
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
        'testId' => array(
            'mapper' => array(
                'testField2',
                'Tests\TestHelpers\Model\Mapper\Db\DbMockMapper2',
            ),
            'dataSource' => array(
                'table' => array('testJoin' => 'test'),
                'type' => Select::JOIN_INNER,
                'on' => array(
                    'id' => 'id',
                ),
                'columns' => array(
                    'testId' => 'id',
                    'testField1' => 'field1',
                    'testField2' => 'field2',
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
