<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Library\Collection\GatewayTracker;
use Library\Log\DummyLogger;
use Library\Model\Mapper\Db\AbstractMapper;
use Library\Model\Mapper\Db\MapperInterface;
use Library\Model\Mapper\Db\TableInterface;
use Zend\Cache\Pattern\ObjectCache;
use Zend\Cache\PatternFactory;
use Zend\Cache\Storage\Adapter\Memory as MemoryCache;
use Zend\Cache\Storage\Plugin\ClearExpiredByFactor;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Sql;
use Zend\Log\LoggerInterface;
use Zend\Cache\Storage\Adapter\Filesystem as FilesystemCache;

abstract class AbstractTableGateway extends TableGateway implements TableInterface
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
    protected $tableTracker;

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
     * Constructor
     *
     * @param AdapterInterface $adapter
     * @param string $table
     * @param mixed $features
     * @param ResultSetInterface $resultSetPrototype
     * @param Sql $sql
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

    /**
     * @return ObjectCache
     */
    public function getCache()
    {
        if (empty($this->cache)) {
            $plugin = new ClearExpiredByFactor();
            $plugin->getOptions()->setClearingFactor(1);

            $cacheStorage = new FilesystemCache(array(
                'cache_dir' => 'module/Tests/caches',
                'ttl' => 10,
            ));

//            $cacheStorage = new MemoryCache();
            $cacheStorage->addPlugin($plugin);

            $this->cache = PatternFactory::factory(
                'object',
                array(
                    'object' => $this,
                    'storage' => $cacheStorage,
                    'cache_output' => false,
                )
            );
        }

        return $this->cache;
    }

    /**
     * This is just a proxy method to facilitate the auto-complete
     *
     * @return AbstractTableGateway
     */
    public function cache()
    {
        return $this->getCache();
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
            ->setMapper($this->getMapper())
            ->setLogger($this->getLogger())
            ->setCache(clone $this->getCache());

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
     * @param \Library\Model\Mapper\Db\MapperInterface $mapper
     * @return $this
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;

        // Updating the dependencies of the other objects
        if (!empty($this->processorPrototype)) {
            $this->processorPrototype->setMapper($this->mapper);
        }

        // To avoid a loop we only set the dataSource it hasn't been set
        // We do this because a dataSource can be attached to a mapper and vice-versa
        if ($this->mapper->getDataSource() !== $this) {
            $this->mapper->setDataSource($this);
        }

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
     * Select
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
     * @return Select
     */
    public function getSelect()
    {
        if (empty($this->select)) {
            $this->select = $this->getSql()->select();

            // Gateway might not require a mapper
            if (!empty($this->mapper)) {
                $this->mapper->prepareSelect();
//                $this->tableTracker->linkWith($this);
            }
        }

        return $this->select;
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

        $resultSet    = parent::executeSelect($select);
        $this->select = null;

        return $resultSet;
    }

    /**
     * Method is used to add the JOIN statements to the provided $select object
     * The $specs represents the information on how to join the objects
     *
     * @param TableInterface $rootDataSource
     * @param array $specs
     * @return AbstractTableGateway
     */
    public function enhanceSelect(TableInterface $rootDataSource, array $specs)
    {
        // This is executed by the gateways that are attached to the child mappers
        if (empty($this->select)) {
            $this->select = $rootDataSource->getSelect();
        }

        // Building the join data
        $on = '';

        foreach ($specs['on'] as $leftField => $rightField) {
            if (strpos($leftField, '.') === false) {
                $leftField = $rootDataSource->getTable() . '.' . $leftField;
            }

            if (strpos($rightField, '.') === false) {
                $rightTableName = $specs['table'];
                if (is_array($rightTableName)) {
                    $rightTableName = key($rightTableName);
                }

                $rightField = $rightTableName . '.' . $rightField;
            }

            if (!empty($on)) {
                $on .= ' AND ';
            }

            $on .= $leftField . ' = ' . $rightField;
        }

        $this->getSelect()->join($specs['table'], $on, $specs['columns'], $specs['type']);

        return $this;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        $result    = null;
        $select    = $this->getSelect()->where(array($this->table . '.id' => $id));
        $processor = clone $this->getProcessorPrototype();

        $resultSet = $processor->setSelect($select)->getResultSet();

        if ($resultSet !== null && $resultSet->count() > 0) {
            $result = $resultSet->current();
        }

        return $result;
    }

    /**
     * @return ResultProcessor
     */
    public function fetch()
    {
        $resultSet = null;
        $select    = $this->getSelect();
        $processor = clone $this->getProcessorPrototype();

        return $processor->setSelect($select);
    }
}
