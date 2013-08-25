<?php
/**
 * ZF2-AuthModule
 *
 */

namespace Tests\TestHelpers\Db\Adapter;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver;
use Zend\Db\Adapter\Platform\PlatformInterface;

class Adapter implements AdapterInterface
{
    /**
     * @return Driver\DriverInterface
     */
    public function getDriver()
    {
        return '';
    }

    /**
     * @return PlatformInterface
     */
    public function getPlatform()
    {
        return '';
    }
}
