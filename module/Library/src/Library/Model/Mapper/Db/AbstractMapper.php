<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper\Db;

use Library\Model\Mapper\AbstractMapper as StandardAbstractMapper;
use Zend\Db\TableGateway\TableGateway;

abstract class AbstractMapper extends StandardAbstractMapper implements MapperInterface
{
    /**
     * @var TableGateway
     */
    protected $dataSource;

    /**
     *
     * @param TableGateway $dataSource
     */
    public function __construct(TableGateway $dataSource)
    {
        $this->setDataSource($dataSource);
    }

    /**
     * @param TableGateway $dataSource
     * @return AbstractDbMapper
     * @return $this|\Library\Model\Mapper\DbMapperInterface
     */
    public function setDataSource(TableGateway $dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        $result = $this->getDataSource()->select(array('id' => $id));

        return $this->populate($result->current()->getArrayCopy());
    }
}
