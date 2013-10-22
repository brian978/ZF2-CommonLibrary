<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Zend\Cache\Pattern\ObjectCache;

class TableGateway extends AbstractTableGateway
{
    /**
     * @return ObjectCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * This is just a proxy method to facilitate the auto-complete
     *
     * @throws \RuntimeException
     * @return TableGateway
     */
    public function cache()
    {
        if($this->getCache() == null) {
            throw new \RuntimeException('No cache object has been set');
        }

        return $this->getCache();
    }
}
