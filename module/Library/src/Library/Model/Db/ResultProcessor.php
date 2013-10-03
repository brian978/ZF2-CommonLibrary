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
use Library\Paginator\Adapter\DbSelect;
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
     * @param \Library\Model\Mapper\Db\AbstractMapper $mapper
     * @return ResultProcessor
     */
    public function setMapper($mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * @return \Library\Model\Mapper\Db\AbstractMapper
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
     * @param null|\Zend\Db\Sql\Select $customSelect
     * @return null|ResultSet
     */
    public function getResultSet($map = 'default', Select $customSelect = null)
    {
        $resultSet = null;
        $select    = $this->select;

        if ($customSelect != null) {
            $select = $customSelect;
        }

        try {
            /** @var $resultSet ResultSet */
            $resultSet = $this->getDataSource()->selectWith($select);
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
