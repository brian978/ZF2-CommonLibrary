<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper\Db;

use Library\Model\Db\AbstractTableGateway;
use Library\Model\Mapper\AbstractMapper as StandardAbstractMapper;
use Zend\Db\Sql\Select;

abstract class AbstractMapper extends StandardAbstractMapper implements MapperInterface
{
    /**
     * The map that will be used to populate the object
     *
     * @see AbstractMapperMap.phps
     * @var array
     */
    protected $map = array();

    /**
     * @var TableInterface
     */
    protected $dataSource;

    /**
     *
     * @param TableInterface $dataSource
     * @return \Library\Model\Mapper\Db\AbstractMapper
     */
    public function __construct(TableInterface $dataSource = null)
    {
        if($dataSource !== null) {
            $this->setDataSource($dataSource);
        }
    }

    /**
     * @param TableInterface $dataSource
     * @return AbstractMapper
     * @return \Library\Model\Mapper\Db\AbstractMapper
     */
    public function setDataSource(TableInterface $dataSource)
    {
        $this->dataSource = $dataSource;

        // To avoid a loop we only set the mapper it hasn't been set
        // We do this because a mapper can be attached to a dataSource and vice-versa
        if($this->dataSource->getMapper() !== $this) {
            $this->dataSource->setMapper($this);
        }

        return $this;
    }

    /**
     * @return AbstractTableGateway
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }
}
