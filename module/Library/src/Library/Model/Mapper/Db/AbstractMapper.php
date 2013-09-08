<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
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
     * @var TableInterface
     */
    protected $dataSource;

    /**
     *
     * @param TableInterface $dataSource
     * @return \Library\Model\Mapper\Db\AbstractMapper
     */
    public function __construct(TableInterface $dataSource)
    {
        $this->setDataSource($dataSource);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \RuntimeException
     */
    public function __call($name, array $arguments)
    {
        if(is_string($name) && is_callable(array($this->dataSource, $name))) {
            return call_user_func_array(array($this->dataSource, $name), $arguments);
        }

        throw new \RuntimeException('Invalid method (' . $name . ') called');
    }

    /**
     * @param TableInterface $dataSource
     * @return AbstractMapper
     * @return $this|\Library\Model\Mapper\DbMapperInterface
     */
    public function setDataSource(TableInterface $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->dataSource->setMapper($this);

        return $this;
    }

    /**
     * @return AbstractTableGateway
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @return Select
     */
    public function prepareSelect()
    {
        foreach ($this->map as $field) {
            if (is_array($field) && $this->useMapper($field)) {
                /** @var $mapper AbstractMapper */
                foreach ($this->mappers as $mapper) {
                    $this->getDataSource()->enhanceSelect(
                        $this->getDataSource(),
                        $mapper->getDataSource(),
                        $field['dataSource']
                    );
                }
            }
        }

        return $this->getDataSource()->getSelect();
    }
}
