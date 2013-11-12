<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Library\Model\Db\Components\GatewayTracker;
use Library\Model\Mapper\AbstractMapper;

interface TableInterface
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
     * @return TableInterface
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
