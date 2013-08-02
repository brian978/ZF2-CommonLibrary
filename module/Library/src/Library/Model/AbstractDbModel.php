<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model;

use Library\Entity\AbstractEntity;
use Library\Entity\EntityInterface;
use Library\Log\DummyLogger;
use Zend\Db\Sql\Select;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

abstract class AbstractDbModel extends AbstractDbHelperModel implements LoggerAwareInterface
{
    /**
     * @var \Zend\Db\Sql\Select
     */
    protected $select;

    /**
     * @var string
     */
    protected $uniqueId = 'id';

    /**
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param $object
     * @return mixed
     */
    abstract protected function doInsert($object);

    /**
     * @param $object
     * @return mixed
     */
    abstract protected function doUpdate($object);

    /**
     * @param $object
     * @return mixed
     */
    abstract public function doDelete($object);

    /**
     * @return \Zend\Log\LoggerInterface
     */
    public function getLogger()
    {
        if(!$this->logger instanceof LoggerInterface) {
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
     * Resets some of the properties of the object
     *
     * @return $this
     */
    protected function resetSelectJoinWhere()
    {
        $this->select = null;

        return parent::resetSelectJoinWhere();
    }

    /**
     * @param $entityId
     *
     * @return \ArrayObject
     */
    public function getInfo($entityId)
    {
        $this->addWhere('id', $entityId);

        $entity = current($this->fetch());

        if (empty($entity)) {
            $entity = array();
        }

        return $entity;
    }

    /**
     *
     * @param EntityInterface $object
     *
     * @return int
     */
    public function save(EntityInterface $object)
    {
        if ($object->getId() === 0) {
            $result = $this->doInsert($object);
        } else {
            $result = $this->doUpdate($object);
        }

        return $result;
    }

    /**
     * @return Select
     */
    public function getSelect()
    {
        if ($this->select instanceof Select === false) {
            $this->select = $this->getSql()->select();

            if (!empty($this->selectColumns)) {
                $this->select->columns($this->selectColumns);
            }
        }

        return $this->select;
    }

    /**
     * This method is used to apply a custom row processing by the child models
     *
     * @param $row
     * @return object
     */
    protected function processRow(\ArrayObject $row)
    {
        return $row;
    }

    /**
     * @param array          $data
     * @param AbstractEntity $object
     *
     * @return int
     */
    protected function executeUpdateById(array $data, AbstractEntity $object)
    {
        $result = 0;

        try {
            // If successful will return the number of rows
            $result = $this->update(
                $data,
                array($this->buildWhere('id', $object->getId()))
            );
        } catch (\Exception $e) {
            $this->getLogger()->err('Update by ID failed with message: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Retrieves all the data from the database
     *
     * @return array
     */
    public function fetch()
    {
        $rows   = array();
        $select = $this->getSelect();

        $select->where($this->where);

        // Adding the joins for the select
        foreach ($this->joins as $join) {
            call_user_func_array(array($select, 'join'), $join);
        }

        try {
            $resultSet = $this->selectWith($select);
        } catch (\Exception $e) {
            $this->getLogger()->err('Select failed with message: ' . $e->getMessage());
            $this->getLogger()->info('Select statement is: ' . $select->getSqlString());
        }

        if (isset($resultSet)) {
            if ($resultSet->count() > 0) {
                foreach ($resultSet as $row) {

                    if (isset($row[$this->uniqueId])) {
                        $rows[$row[$this->uniqueId]] = $this->processRow($row);
                    } else {
                        $rows[] = $this->processRow($row);
                    }
                }
            }

            $this->fetchRun = true;
        }

        return $rows;
    }
}
