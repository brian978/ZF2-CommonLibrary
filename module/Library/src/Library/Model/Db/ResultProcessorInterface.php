<?php
/**
 * ZF2-ExtendedFramework
 */
namespace Library\Model\Db;

use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerInterface;
use Zend\Paginator\Paginator;

interface ResultProcessorInterface
{
    const EVENT_PROCESS_ROW = 'processRow';
    const EVENT_CHANGE_MAP  = 'changeMap';

    /**
     * @return Paginator
     */
    public function getPaginator();

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
     * @param \Zend\Log\LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @param EventManagerInterface $eventManager
     * @return $this
     */
    public function setEventManager(EventManagerInterface $eventManager);

    /**
     * @return EventManager
     */
    public function getEventManager();
}
