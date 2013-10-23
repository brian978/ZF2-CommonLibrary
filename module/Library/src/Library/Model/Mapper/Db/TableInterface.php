<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper\Db;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;

interface TableInterface extends TableGatewayInterface
{
    /**
     * @param MapperInterface $mapper
     * @return mixed
     */
    public function setMapper(MapperInterface $mapper);

    /**
     * @return MapperInterface
     */
    public function getMapper();

    /**
     * Returns the name of the table
     *
     * @return string
     */
    public function getTable();

    /**
     * @return Select
     */
    public function getSelect();
}
