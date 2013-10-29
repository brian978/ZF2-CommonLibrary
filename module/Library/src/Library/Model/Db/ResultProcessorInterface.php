<?php
/**
 * ZF2-ExtendedFramework
 */
namespace Library\Model\Db;

use Zend\Db\ResultSet\ResultSet;
use Zend\Log\LoggerInterface;
use Zend\Paginator\Paginator;

interface ResultProcessorInterface
{
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
}
