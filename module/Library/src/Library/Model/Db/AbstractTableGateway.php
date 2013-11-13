<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Library\Model\Db\Collection\GatewayTracker;
use Library\Log\DummyLogger;
use Library\Model\Mapper\AbstractMapper;
use Library\Model\Mapper\MapperInterface;
use Zend\Cache\Pattern\ObjectCache;
use Zend\Cache\Storage\PostEvent;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Sql;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerInterface;

abstract class AbstractTableGateway extends TableGateway implements TableGatewayInterface
{
    /**
     * @var Select
     */
    protected $select;

    /**
     * @var AbstractMapper
     */
    protected $mapper;

    /**
     * @var GatewayTracker
     */
    protected $tracker;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ResultProcessor
     */
    protected $processorPrototype;

    /**
     * @var ObjectCache
     */
    protected $cache;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * Constructor
     *
     * @param AdapterInterface $adapter
     * @param string $table
     * @param mixed $features
     * @param ResultSetInterface $resultSetPrototype
     * @param \Zend\Db\Sql\Sql $sql
     * @return \Library\Model\Db\AbstractTableGateway
     */
    public function __construct(
        AdapterInterface $adapter,
        $table = null,
        $features = null,
        ResultSetInterface $resultSetPrototype = null,
        Sql $sql = null
    ) {
        if (!empty($this->table) && empty($table)) {
            $table = $this->table;
        }

        parent::__construct($table, $adapter, $features, $resultSetPrototype, $sql);
    }

    public function __sleep()
    {
        return array('select', 'mapper', 'tracker', 'logger', 'processorPrototype');
    }

    /**
     * Select
     *
     * We override this to make it use the getSelect() method
     *
     * @param \Zend\Db\Sql\Where|\Closure|string|array $where
     * @return ResultSet
     */
    public function select($where = null)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }

        $select = $this->getSelect();

        if ($where instanceof \Closure) {
            $where($select);
        } elseif ($where !== null) {
            $select->where($where);
        }

        return $this->selectWith($select);
    }

    /**
     * @param \Library\Model\Db\Collection\GatewayTracker $tracker
     *
     * @return AbstractTableGateway
     */
    public function setTracker($tracker)
    {
        $this->tracker = $tracker;

        return $this;
    }

    /**
     * @return \Library\Model\Db\Collection\GatewayTracker
     */
    public function getTracker()
    {
        return $this->tracker;
    }

    /**
     * @param EventManagerInterface $eventManager
     * @return $this
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->setIdentifiers(array(__CLASS__, get_called_class()));

        return $this;
    }

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }

    /**
     * @return ObjectCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Alias to getCache() for easier calling
     *
     * @return $this
     */
    public function cache()
    {
        return $this->getCache();
    }

    /**
     * //TODO: find better way to set the object cache
     *
     * @param ObjectCache $cache
     * @return $this
     */
    public function setCache(ObjectCache $cache)
    {
        $this->cache = $cache;
        $this->cache->getOptions()->setObject($this);

        // Setting up some wake-up events
        /** @var $cacheEventManager EventManager */
        $cacheEventManager = $this->cache->getOptions()->getStorage()->getEventManager();
        $cacheEventManager->attach(
            'getItem.post',
            function (PostEvent $event) {
                if ($event->getResult() instanceof ResultProcessorInterface) {
                }
            }
        );

        return $this;
    }

    /**
     * @param \Library\Model\Db\ResultProcessor $processorPrototype
     * @return AbstractTableGateway
     */
    public function setProcessorPrototype($processorPrototype)
    {
        $this->processorPrototype = $processorPrototype->setDataSource($this);

        // Injecting the dependencies
        $this->processorPrototype
            ->setLogger($this->getLogger());

        return $this;
    }

    /**
     * @return \Library\Model\Db\ResultProcessor
     */
    public function getProcessorPrototype()
    {
        if (empty($this->processorPrototype)) {
            $this->setProcessorPrototype(new ResultProcessor());
        }

        return $this->processorPrototype;
    }

    /**
     * @param \Library\Model\Mapper\MapperInterface $mapper
     * @return $this
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * @return AbstractMapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * @return \Zend\Log\LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->logger instanceof LoggerInterface) {
            $this->logger = new DummyLogger();
        }

        return $this->logger;
    }

    /**
     * @param \Zend\Log\LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        // Updating the dependencies of the other objects
        if (!empty($this->processorPrototype)) {
            $this->processorPrototype->setLogger($this->logger);
        }

        return $this;
    }

    /**
     * @return Select
     */
    public function getSelect()
    {
        if (empty($this->select)) {
            $this->select = $this->getSql()->select();
        }

        return $this->select;
    }

    /**
     * @return ResultProcessor
     */
    protected function getProcessorClone()
    {
        $processor = clone $this->getProcessorPrototype();
        $processor->setSelect($this->getSelect());

        return $processor;
    }

    /**
     * @param array $rawJoins
     * @param \Library\Model\Db\Collection\GatewayTracker $tracker
     * @throws \InvalidArgumentException
     * @return $this
     */
    protected function attachMappers(array $rawJoins, GatewayTracker $tracker)
    {
        if (is_null($tracker)) {
            throw new \InvalidArgumentException(
                'The provided tracker is NULL. Should be instance of \Library\Model\Db\Collection\GatewayTracker'
            );
        }

        foreach ($rawJoins as $join) {
            if (is_array($join['name'])) {
                $tableName = current($join['name']);
            } else {
                $tableName = $join['name'];
            }

            try {
                $gateway = $tracker->getGateway($tableName);
                $mapper  = $gateway->getMapper();
                $this->getMapper()->attachMapper($mapper);
            } catch (\InvalidArgumentException $e) {
                $this->getLogger()->debug($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->getLogger()->debug($e->getMessage());
            }
        }

        return $this;
    }

    /**
     * Overwritten the method so we can reset the select after each execution
     * to avoid making 2 consecutive selects that mix data from one another
     *
     * @param Select $select
     * @return ResultSet
     * @throws \RuntimeException
     */
    protected function executeSelect(Select $select)
    {
        // Need to clone the platform to avoid random opened connections (especially when testing)
        $platform = clone $this->getAdapter()->getPlatform();
        $this->getLogger()->debug($select->getSqlString($platform));

        $tracker = $this->getTracker();
        if ($tracker !== null) {
            $this->attachMappers($select->getRawState(Select::JOINS), $tracker);
        }

        $resultSet    = parent::executeSelect($select);
        $this->select = null;

        return $resultSet;
    }

    /**
     * Returns a single (mapped or not) entry in the database
     *
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        $processor = $this->getProcessorClone();
        $processor->getSelect()->where(array($this->getTable() . '.id' => $id));

        $resultSet = $processor->getResultSet();
        if ($resultSet !== null && $resultSet->count() > 0) {
            return $resultSet->current();
        }

        return null;
    }

    /**
     * Returns all the information in the database
     *
     * @return ResultProcessor
     */
    public function fetch()
    {
        return $this->getProcessorClone();
    }
}
