<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper\Db;

use Library\Model\Entity\EntityInterface;
use Library\Model\Mapper\MapperInterface as StandardMapperInterface;

interface MapperInterface extends StandardMapperInterface
{
    /**
     * @param TableInterface $dataSource
     * @return MapperInterface
     */
    public function setDataSource(TableInterface $dataSource);

    /**
     * @param EntityInterface $object
     * @return mixed
     */
    public function save(EntityInterface $object);
}
