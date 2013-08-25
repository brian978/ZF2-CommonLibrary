<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Traits;

trait AdapterTrait
{
    /**
     * @var \Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @return \Zend\Db\Adapter\AdapterInterface
     */
    protected function getAdapter()
    {
        if (empty($this->adapter)) {

            // Creating the mocks
            $this->adapter = $this->getMockBuilder('\Zend\Db\Adapter\AdapterInterface')
                ->getMock();

            $platform = $this->getMockBuilder('\Zend\Db\Adapter\Platform\PlatformInterface')
                ->getMock();

            $driver = $this->getMockBuilder('\Zend\Db\Adapter\Driver\DriverInterface')
                ->getMock();

            $connection = $this->getMockBuilder('\Zend\Db\Adapter\Driver\ConnectionInterface')
                ->getMock();

            /**
             * -----------------------------
             * DRIVER SETUP
             * -----------------------------
             */
            $driver->expects($this->any())
                ->method('getConnection')
                ->will($this->returnValue($connection));

            /**
             * -----------------------------
             * PLATFORM SETUP
             * -----------------------------
             */
            $platform->expects($this->any())
                ->method('quoteIdentifierChain')
                ->will(
                    $this->returnCallback(
                        function ($identifierChain) {
                            return implode('.', $identifierChain);
                        }
                    )
                );

            $platform->expects($this->any())
                ->method('quoteValue')
                ->will($this->returnArgument(0));

            /**
             * -----------------------------
             * ADAPTER SETUP
             * -----------------------------
             */
            $this->adapter->expects($this->any())
                ->method('getDriver')
                ->will($this->returnValue($driver));

            $this->adapter->expects($this->any())
                ->method('getPlatform')
                ->will($this->returnValue($platform));
        }

        return $this->adapter;
    }
}
