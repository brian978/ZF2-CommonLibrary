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
