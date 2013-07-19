<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Exception\RuntimeException;

class AbstractDbHelperModel extends AbstractTableGateway
{
    /**
     * Connection object
     *
     * @var \Zend\Db\Adapter\Driver\ConnectionInterface
     */
    protected $connection;

    /**
     * Adapter platform
     *
     * @var \Zend\Db\Adapter\Platform\PlatformInterface
     */
    protected $platform;

    /**
     * An array of where conditions used by the fetch method
     *
     * @var array
     */
    protected $where = array();

    /**
     * An array joins used by the fetch method
     *
     * @var array
     */
    protected $joins = array();

    /**
     * This is used to determine when to reset the where condition
     *
     * @var boolean
     */
    protected $fetchRun = false;

    /**
     * @var array
     */
    protected $selectColumns = array();

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        if ($adapter !== null) {
            $this->setAdapter($adapter);
            $this->initialize();
        }
    }

    /**
     * @return null|void
     * @throws \Zend\Db\TableGateway\Exception\RuntimeException
     */
    public function initialize()
    {
        if ($this->isInitialized) {
            return;
        }

        parent::initialize();

        if (!$this->platform instanceof PlatformInterface) {
            throw new RuntimeException('This table does not have an Platform setup.');
        }

        if (!$this->connection instanceof ConnectionInterface) {
            throw new RuntimeException('This table does not have an Connection setup.');
        }
    }

    /**
     * @param AdapterInterface $adapter
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter    = $adapter;
        $this->platform   = $this->adapter->getPlatform();
        $this->connection = $this->adapter->getDriver()->getConnection();

        return $this;
    }


    /**
     * @return $this
     */
    protected function resetSelectJoinWhere()
    {
        $this->where    = array();
        $this->joins    = array();
        $this->fetchRun = false;

        return $this;
    }

    /**
     * Used to generate a where condition
     *
     * @param string $field
     * @param string|int $value
     * @param string $table
     * @param string $sign
     * @return string
     */
    public function buildWhere($field, $value, $table = null, $sign = '=')
    {
        // When the value is an object it's because of an expression
        if (is_object($value)) {
            return $value;
        }

        if ($table === null) {
            $table = $this->table;
        }

        $identifierChain = array(
            $table,
            $field
        );

        return $this->platform->quoteIdentifierChain(
            $identifierChain
        ) . $sign . $this->platform->quoteValue($value);
    }

    /**
     * Used to add a where condition
     *
     * @param string|array $field
     * @param string|int|bool $value When this is set to true the $field param in an array
     * @param string $table
     *
     * @return $this
     */
    public function addWhere($field, $value, $table = null)
    {
        // Resetting the where if the fetch method has already run
        if ($this->fetchRun === true) {
            $this->resetSelectJoinWhere();
        }

        if (is_array($field) && $value === true) { // We have an array of where conditions
            $this->where = array_merge($this->where, $field);
        } elseif (is_string($field) && $value === true) { // The string has already been made
            $this->where[] = $field;
        } else { // We build the where "manually"
            $this->where[] = $this->buildWhere($field, $value, $table);
        }

        return $this;
    }

    /**
     * Used to add a join condition
     *
     * @param        $name
     * @param        $condition
     * @param string $columns
     * @param string $type
     *
     * @return $this
     */
    public function addJoin($name, $condition, $columns = Select::SQL_STAR, $type = Select::JOIN_INNER)
    {
        // Resetting the where if the fetch method has already run
        if ($this->fetchRun === true) {
            $this->resetSelectJoinWhere();
        }

        $this->joins[] = array(
            $name,
            $condition,
            $columns,
            $type
        );

        return $this;
    }

    /**
     * Used to add a column for the select
     *
     * @param array $columns
     *
     * @return $this
     */
    public function addColumns(array $columns)
    {
        $this->selectColumns = array_merge($this->selectColumns, $columns);

        return $this;
    }
}
