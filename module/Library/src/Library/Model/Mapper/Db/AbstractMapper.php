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

abstract class AbstractMapper extends StandardAbstractMapper implements MapperInterface
{
    /**
     * @var TableInterface
     */
    protected $dataSource;

    /**
     *
     * @param TableInterface $dataSource
     */
    public function __construct(TableInterface $dataSource)
    {
        $this->setDataSource($dataSource);
    }

    /**
     * @param TableInterface $dataSource
     * @return AbstractDbMapper
     * @return $this|\Library\Model\Mapper\DbMapperInterface
     */
    public function setDataSource(TableInterface $dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * @return TableInterface
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
