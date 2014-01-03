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

class DbMockMapper3 extends AbstractMapper
{
    /**
     * Class name of the entity that the data will be mapped to
     *
     * @var string
     */
    protected $entityClass = '\Tests\TestHelpers\Model\Entity\MappedMockEntity';

    /**
     * The map that will be used to populate the object
     *
     * @var array
     */
    protected $map = array(
        'testNewId' => 'id',
        'testNewField1' => 'testField1',
        'testNewField2' => 'testField2',
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
