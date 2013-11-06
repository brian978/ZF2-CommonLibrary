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
use Library\Paginator\Adapter\DbSelect;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Log\LoggerInterface;
use Zend\Paginator\Paginator;

class ResultProcessor implements ResultProcessorInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AbstractTableGateway
     */
    protected $dataSource;

    /**
     * @var Select
     */
    protected $select;

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
     * @param \Library\Model\Db\AbstractTableGateway $dataSource
     *
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
     * @param \Zend\Db\Sql\Select $select
     *
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
     * The method can create either a resultSet with mapped entities or return a set of data
     * like they are in the database (standard ResultSet)
     *
     * @param ResultSet $resultSet
     * @param string $map
     * @return ResultSet
     */
    public function processResultSet(ResultSet $resultSet, $map = 'default')
    {
        $dataSourceMapper = $this->getDataSource()->getMapper();

        if ($dataSourceMapper === null) {
            return $resultSet;
        }

        // Processing the result set using the mapper
        $rows = array();

        /** @var $row \ArrayObject */
        foreach ($resultSet as $row) {
            $populateRow = $dataSourceMapper->populate($row->getArrayCopy(), $map);

            $this->getDataSource()->getEventManager()->trigger(
                'processedRow',
                $this->getDataSource(),
                array($populateRow)
            );

            $rows[] = $populateRow;
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
