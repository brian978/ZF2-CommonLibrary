<?php
/**
 * ZF2-ExtendedFramework
 */
namespace Library\Model\Db;

use Zend\Cache\Pattern\ObjectCache;
use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerInterface;
use Zend\Paginator\Paginator;

interface ResultProcessorInterface
{
    /**
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect();

    /**
     * @return EventManager
     */
    public function getEventManager();

    /**
     * @param \Zend\Cache\Pattern\ObjectCache $cache
     * @return ResultProcessor
     */
    public function setCache(ObjectCache $cache);

    /**
     * @return Paginator
     */
    public function getPaginator();

    /**
     * @return \Zend\Cache\Pattern\ObjectCache
     */
    public function getCache();

    /**
     * @param \Library\Model\Db\AbstractTableGateway $dataSource
     * @return ResultProcessor
     */
    public function setDataSource($dataSource);

    /**
     * @param \Zend\Db\Sql\Select $select
     * @return ResultProcessor
     */
    public function setSelect($select);

    /**
     * @param \Zend\EventManager\EventManagerInterface $eventManager
     * @return $this
     */
    public function setEventManager(EventManagerInterface $eventManager);

    /**
     * @param string $map
     * @return null|ResultSet
     */
    public function getResultSet($map = 'default');

    /**
     * The method can create either a resultSet with mapped entities or return a set of data
     * like they are in the database (standard ResultSet)
     *
     * @param ResultSet $resultSet
     * @param string $map
     * @return ResultSet
     */
    public function processResultSet(ResultSet $resultSet, $map = 'default');

    /**
     * @return \Zend\Log\LoggerInterface
     */
    public function getLogger();

    /**
     * This is just a proxy method to facilitate the auto-complete
     *
     * @throws \RuntimeException
     * @return ResultProcessor
     */
    public function cache();

    /**
     * @return \Library\Model\Db\AbstractTableGateway
     */
    public function getDataSource();

    /**
     * @param \Zend\Log\LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger);
}
