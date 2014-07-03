<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Model\Db\Collection;

use Acamar\Collection\AbstractCollection;
use Acamar\Model\Db\TableGatewayInterface;

class GatewayTracker extends AbstractCollection
{
    /**
     * @var GatewayTracker
     */
    protected static $instance = null;

    /**
     * It uses a Singleton pattern to give the Gateways and auto-track capability
     *
     * @return GatewayTracker
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param TableGatewayInterface $gateway
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function track(TableGatewayInterface $gateway)
    {
        $table = $gateway->getTable();

        // Registering the gateway
        if (!isset($this->collection[$table])) {
            $this->collection[$table] = $gateway->setTracker($this);
        } elseif ($this->collection[$table] !== $gateway) {
            throw new \InvalidArgumentException('The table (' . $table . ') is tracked using another gateway.');
        }

        return $this;
    }

    /**
     * @param $table
     * @return TableGatewayInterface
     * @throws \RuntimeException
     */
    public function getGateway($table)
    {
        /** @var $object TableGatewayInterface */
        foreach ($this->collection as $tableName => $object) {
            if ($tableName === $table) {
                return $object;
            }
        }

        throw new \RuntimeException('A gateway for the requested table (' . $table . ') was not found');
    }
}
