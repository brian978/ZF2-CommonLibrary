<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper;

use Zend\Db\TableGateway\TableGateway;

interface DbMapperInterface extends MapperInterface
{
    /**
     * @param TableGateway $dataSource
     * @return DbMapperInterface
     */
    public function setDataSource(TableGateway $dataSource);

}
