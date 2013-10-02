<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Library\Log\DummyLogger;
use Library\Model\Mapper\Db\AbstractMapper;
use Library\Model\Mapper\Db\MapperInterface;
use Library\Model\Mapper\Db\TableInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Sql;
use Zend\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ResultProcessor
     */
    protected $processorPrototype;

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
     * @param \Library\Model\Db\ResultProcessor $processorPrototype
     * @return AbstractTableGateway
     */
    public function setProcessorPrototype($processorPrototype)
    {
        $this->processorPrototype = $processorPrototype->setDataSource($this)
            ->setMapper($this->getMapper())
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
     * @param \Library\Model\Mapper\Db\MapperInterface $mapper
     * @return mixed
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
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

        return $this;
    }

    /**
     * Select
     *
     * @param Where|\Closure|string|array $where
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
        $this->getLogger()->debug($select->getSqlString($this->getAdapter()->getPlatform()));

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

        if ($resultSet->count() > 0) {
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
