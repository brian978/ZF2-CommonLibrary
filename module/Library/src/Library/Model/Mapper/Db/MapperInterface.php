<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper\Db;

use Library\Model\Entity\EntityInterface;
use Library\Model\Mapper\MapperInterface as StandardMapperInterface;
use Zend\Db\Sql\Select;

interface MapperInterface extends StandardMapperInterface
{
    /**
     * @param TableInterface $dataSource
     * @return MapperInterface
     */
    public function setDataSource(TableInterface $dataSource);

    /**
     * @return TableInterface
     */
    public function getDataSource();

    /**
     * @param EntityInterface $object
     * @return mixed
     */
    public function save(EntityInterface $object);

    /**
     * Calls all the data sources from all the mappers and prepares the select statement
     *
     * @return mixed
     */
    public function prepareSelect();
}
