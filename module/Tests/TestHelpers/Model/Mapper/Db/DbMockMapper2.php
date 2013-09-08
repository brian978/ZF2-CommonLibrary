<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
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
        'id' => 'id',
        'testField1' => 'testField1',
        'testField2' => 'testField2',
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
