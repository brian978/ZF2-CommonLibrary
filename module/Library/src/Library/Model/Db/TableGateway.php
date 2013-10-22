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
use Zend\Cache\PatternFactory;
use Zend\Cache\Storage\Plugin\ClearExpiredByFactor;
use Zend\Cache\Storage\Adapter\Filesystem as FilesystemCache;

class TableGateway extends AbstractTableGateway
{
    /**
     * @return ObjectCache
     */
    public function getCache()
    {
        if (empty($this->cache)) {
            $plugin = new ClearExpiredByFactor();
            $plugin->getOptions()->setClearingFactor(3);

            // Cache storage setup
            $cacheStorage = new FilesystemCache(array(
                'cache_dir' => 'module/Tests/caches',
                'ttl' => 10,
            ));

            $cacheStorage->addPlugin($plugin);

            $this->cache = PatternFactory::factory(
                'object',
                array(
                    'object' => $this,
                    'storage' => $cacheStorage,
                    'cache_output' => false,
                    'cache_by_default' => true,
                )
            );
        }

        return $this->cache;
    }

    /**
     * This is just a proxy method to facilitate the auto-complete
     *
     * @return TableGateway
     */
    public function cache()
    {
        return $this->getCache();
    }

    /**
     * THIS IS A DUMMY METHOD TO TEST THE CACHE LIMITATIONS
     *
     */
    public function dummyMethod()
    {
        return 1;
    }
}
