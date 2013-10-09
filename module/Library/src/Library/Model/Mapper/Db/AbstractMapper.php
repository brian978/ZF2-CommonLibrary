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
    public function __construct(TableInterface $dataSource)
    {
        $this->setDataSource($dataSource);
    }

    /**
     * @param TableInterface $dataSource
     * @return AbstractMapper
     * @return \Library\Model\Mapper\Db\AbstractMapper
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
     * Makes sure that the select has been modified if necessary by all the mappers
     *
     * @return \Library\Model\Mapper\Db\AbstractMapper
     */
    public function prepareSelect()
    {
        foreach ($this->map as $field) {

            // Checking if the field uses a mapper so we know
            // if we dispatch a join request to it
            if (is_array($field) && $this->useMapper($field)) {

                // Selecting the baseMapper
                /** @var $baseMapper AbstractMapper */
                if ($this->parentMapper !== null) {
                    $baseMapper = $this->parentMapper;
                } else {
                    $baseMapper = $this;
                }

                /** @var $mapper AbstractMapper */
                $mapper = $this->getMapperFromInfo($field);

                // We do it like this to keep code completion available
                $mapper->prepareSelect()
                    ->getDataSource()
                    ->enhanceSelect($baseMapper->getDataSource(), $field['dataSource']);
            }
        }

        return $this;
    }
}
