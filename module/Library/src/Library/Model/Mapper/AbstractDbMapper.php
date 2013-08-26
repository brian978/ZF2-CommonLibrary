<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper;

use Zend\Db\TableGateway\TableGateway;

abstract class AbstractDbMapper implements DbMapperInterface
{
    /**
     * @var TableGateway
     */
    protected $dataSource;

    /**
     * Class name of the entity that the data will be mapped to
     *
     * @var string
     */
    protected $entityClass = '';

    /**
     * The map that will be used to populate the object
     *
     * @var array
     */
    protected $map = array();

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
     * The map is immutable
     *
     * @param array $map
     * @return AbstractDbMapper
     */
    public function setMap(array $map)
    {
        if (empty($this->map)) {
            $this->map = $map;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * The entity class is immutable
     *
     * @param string $entityClass
     * @return AbstractDbMapper
     */
    public function setEntityClass($entityClass)
    {
        if (empty($this->entityClass) && is_string($entityClass)) {
            $this->entityClass = $entityClass;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
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

    /**
     * @param $property
     * @return mixed
     */
    protected function createSetterNameFromPropertyName($property)
    {
        return preg_replace_callback(
            '/_([a-z])/',
            function ($string) {
                return ucfirst($string);
            },
            'set' . ucfirst($property)
        );
    }

    /**
     * @param mixed $data
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function populate($data)
    {
        if (!is_array($data) && $data instanceof \ArrayIterator === false) {
            $message = 'The $data argument must be either an array or an instance of \ArrayIterator';
            $message .= gettype($data) . ' given';

            throw new \InvalidArgumentException($message);
        }

        if (empty($this->entityClass)) {
            throw new \RuntimeException('The class for the entity has not been set');
        }

        $object = new $this->entityClass();

        // Populating the object
        foreach ($data as $key => $value) {
            if (isset($this->map[$key])) {
                $methodName = $this->createSetterNameFromPropertyName($this->map[$key]);
                if (is_callable(array($object, $methodName))) {
                    $object->$methodName($value);
                }
            }
        }

        return $object;
    }
}
