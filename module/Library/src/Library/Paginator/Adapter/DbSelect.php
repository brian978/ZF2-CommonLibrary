<?php
/**
 * lida_cleaning
 *
 * @link      https://github.com/brian978/lida_cleaning
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Paginator\Adapter;

use Library\Model\Db\ResultProcessor;
use \Zend\Paginator\Adapter\DbSelect as ZendDbSelect;

class DbSelect extends ZendDbSelect
{
    /**
     * @var ResultProcessor
     */
    protected $processor;

    /**
     * @param \Library\Model\Db\ResultProcessor $processor
     * @return DbSelect
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;

        return $this;
    }

    /**
     * @return \Library\Model\Db\ResultProcessor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  int $offset           Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $select = clone $this->select;
        $select->offset($offset);
        $select->limit($itemCountPerPage);

        if ($this->processor !== null) {
            $resultSet = $this->getProcessor()->setSelect($select)->getResultSet();
        } else {
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $result    = $statement->execute();

            $resultSet = clone $this->resultSetPrototype;
            $resultSet->initialize($result);
        }

        return $resultSet;
    }
}
