<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Model\Db;

use Acamar\Model\Db\Collection\GatewayTracker;
use Acamar\Model\Mapper\AbstractMapper;
use Zend\Db\TableGateway\TableGatewayInterface as ZendTableGatewayInterface;

interface TableGatewayInterface extends ZendTableGatewayInterface
{
    /**
     * Get table name
     *
     * @return string
     */
    public function getTable();

    /**
     * @param GatewayTracker $tracker
     *
     * @return TableGatewayInterface
     */
    public function setTracker($tracker);

    /**
     * @return GatewayTracker
     */
    public function getTracker();

    /**
     * @return AbstractMapper
     */
    public function getMapper();
}
