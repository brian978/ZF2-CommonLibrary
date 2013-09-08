<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Library\Model\Mapper\Db\AbstractMapper;
use Library\Model\Mapper\Db\TableInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Sql;

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
     * Constructor
     *
     * @param string $table
     * @param AdapterInterface $adapter
     * @param \Zend\Db\TableGateway\Feature\AbstractFeature|\Zend\Db\TableGateway\Feature\FeatureSet|\Zend\Db\TableGateway\Feature\AbstractFeature[] $features
     * @param ResultSetInterface $resultSetPrototype
     * @param \Zend\Db\Sql\Sql $sql
     * @param Sql $sql
     */
    public function __construct(
        $table = null,
        AdapterInterface $adapter,
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
     * @param AbstractMapper $mapper
     * @return mixed
     */
    public function setMapper(AbstractMapper $mapper)
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
            $this->getMapper()->prepareSelect();
        }

        return $this->select;
    }

    /**
     * Method is used to add the JOIN statements to the provided $select object
     * The data represents the information on how to join the objects
     *
     * @param AbstractTableGateway $rootDataSource
     * @param array $data
     * @return AbstractTableGateway
     */
    public function enhanceSelect(AbstractTableGateway $rootDataSource, array $data)
    {
        // This is executed by the gateways that are attached to the child mappers
        if (empty($this->select)) {
            $this->select = $rootDataSource->getSelect();
        }

        // Building the join data
        $on = '';

        foreach ($data['on'] as $leftField => $rightField) {
            if (strpos($leftField, '.') === false) {
                $leftField = $rootDataSource->getTable() . '.' . $leftField;
            }

            if (strpos($rightField, '.') === false) {
                $rightTableName = $data['table'];
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

        $this->getSelect()->join($data['table'], $on, $data['columns'], $data['type']);

        return $this;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        $select = $this->getSelect();
        $result = $this->selectWith($select->where(array($this->table . '.id' => $id)));

//        echo $select->getSqlString($this->getAdapter()->getPlatform());

        return $this->getMapper()->populate($result->current()->getArrayCopy());
    }
}
