<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Library\Model\Mapper\Db\TableInterface;

class GatewayTracker
{
    /**
     * @var array
     */
    protected $trackedGateways = array();

    /**
     * @var bool
     */
    protected $trackingCompleted = false;

    /**
     * @return bool
     */
    public function isTrackingCompleted()
    {
        return $this->trackingCompleted;
    }

    public function track($tableName, TableInterface $gateway)
    {
    }
}
