<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Library\Log\DummyLogger;
use Library\Model\Mapper\Db\AbstractMapper;
use Library\Paginator\Adapter\DbSelect;
use Zend\Cache\Pattern\ObjectCache;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerInterface;
use Zend\Paginator\Paginator;

class ResultProcessor
{
    /**
     * @var Select
     */
    protected $select;

    /**
     * @var AbstractTableGateway
     */
    protected $dataSource;

    /**
     * @var AbstractMapper
     */
    protected $mapper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var ObjectCache
     */
    protected $cache;

    /**
     * @param \Zend\EventManager\EventManagerInterface $eventManager
     * @return $this
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(
            array(
                __CLASS__,
                get_called_class(),
            )
        );
        $this->eventManager = $eventManager;

        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }

    /**
     * @param \Library\Model\Db\AbstractTableGateway $dataSource
     * @return ResultProcessor
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * @return \Library\Model\Db\AbstractTableGateway
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param \Library\Model\Mapper\Db\MapperInterface $mapper
     * @return ResultProcessor
     */
    public function setMapper($mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * @return \Library\Model\Mapper\Db\MapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * @param \Zend\Db\Sql\Select $select
     * @return ResultProcessor
     */
    public function setSelect($select)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        return $this->select;
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

        return $this;
    }

    /**
     * @param \Zend\Cache\Pattern\ObjectCache $cache
     * @return ResultProcessor
     */
    public function setCache(ObjectCache $cache)
    {
        $this->cache = $cache;

        // Updating the cached object (might already be set to the proper one)
        $this->cache->getOptions()->setObject($this);

        return $this;
    }

    /**
     * @return \Zend\Cache\Pattern\ObjectCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * This is just a proxy method to facilitate the auto-complete
     *
     * @return ResultProcessor
     */
    public function cache()
    {
        return $this->getCache();
    }

    /**
     * The method can create either a resultSet with mapped entities or return a set of data
     * like they are in the database (standard ResultSet)
     *
     * @param ResultSet $resultSet
     * @param string $map
     * @return ResultSet
     */
    public function processResultSet(ResultSet $resultSet, $map = 'default')
    {
        if ($this->mapper === null) {
            return $resultSet;
        }

        // Processing the result set using the mapper
        $rows = array();

        /** @var $row \ArrayObject */
        foreach ($resultSet as $row) {
            $rows[] = $this->getMapper()->populate($row->getArrayCopy(), $map);
        }

        return $resultSet->initialize(new \ArrayIterator($rows));
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        $paginatorAdapter = new DbSelect($this->select, $this->getDataSource()->getSql());
        $paginatorAdapter->setProcessor($this);

        return new Paginator($paginatorAdapter);
    }

    /**
     * @param string $map
     * @return null|ResultSet
     */
    public function getResultSet($map = 'default')
    {
        $resultSet = null;

        try {
            /** @var $resultSet ResultSet */
            $resultSet = $this->getDataSource()->selectWith($this->select);
        } catch (\Exception $e) {
            $this->getLogger()->err('Select failed with message: ' . $e->getMessage());
        }

        if (!empty($resultSet)) {
            if ($resultSet->count() > 0) {
                $resultSet = $this->processResultSet($resultSet, $map);
            }
        }

        return $resultSet;
    }
}
