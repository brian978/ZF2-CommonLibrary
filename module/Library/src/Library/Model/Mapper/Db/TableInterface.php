<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper\Db;

use Library\Model\Db\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;

interface TableInterface extends TableGatewayInterface
{
    /**
     * @param AbstractMapper $mapper
     * @return mixed
     */
    public function setMapper(AbstractMapper $mapper);

    /**
     * Method is used to add the JOIN statements to the provided $select object
     * The data represents the information on how to join the objects
     *
     * @param AbstractTableGateway $rootDataSource
     * @param AbstractTableGateway $linkDataSource
     * @param array $data
     * @return mixed
     */
    public function enhanceSelect(
        AbstractTableGateway $rootDataSource,
        AbstractTableGateway $linkDataSource,
        array $data
    );
}
