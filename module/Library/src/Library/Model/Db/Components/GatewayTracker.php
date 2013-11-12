<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db\Components;

use Library\Collection\AbstractCollection;
use Library\Model\Db\TableInterface;

class GatewayTracker extends AbstractCollection
{
    /**
     * @param TableInterface $gateway
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function track(TableInterface $gateway)
    {
        $tableName = $gateway->getTable();

        // Registering the gateway
        if (!isset($this->collection[$tableName])) {
            $this->collection[$tableName] = $gateway->setTracker($this);
        } elseif ($this->collection[$tableName] !== $gateway) {
            throw new \InvalidArgumentException('The table is tracked using another gateway.');
        }

        return $this;
    }

    /**
     * @param $table
     * @return TableInterface
     * @throws \RuntimeException
     */
    public function getGateway($table)
    {
        /** @var $object TableInterface */
        foreach ($this->collection as $tableName => $object) {
            if ($tableName === $table) {
                return $object;
            }
        }

        throw new \RuntimeException('A gateway for the requested table (' . $table . ') was not found');
    }
}
