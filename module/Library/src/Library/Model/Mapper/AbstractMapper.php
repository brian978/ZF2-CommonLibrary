<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper;

use Library\Model\Entity\EntityInterface;
use Library\Model\Mapper\Exception\WrongDataTypeException;

class AbstractMapper implements MapperInterface
{
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
     * @var array
     */
    protected $mappers = array();

    /**
     * @var AbstractMapper
     */
    protected $parentMapper = null;

    /**
     * @param array $map
     * @return AbstractDbMapper
     */
    public function setMap(array $map)
    {
        $this->map = $map;

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
     * @param string $entityClass
     * @return AbstractDbMapper
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

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
     * @return \Library\Model\Entity\AbstractEntity
     */
    public function createEntityObject()
    {
        return new $this->entityClass();
    }

    /**
     * @param string $property
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
     * @param EntityInterface $object
     * @param string          $propertyName
     * @param string          $value
     */
    protected function populateUsingString(EntityInterface $object, $propertyName, $value)
    {
        $methodName = $this->createSetterNameFromPropertyName($propertyName);
        if (is_callable(array($object, $methodName))) {
            $object->$methodName($value);
        }
    }

    /**
     * @param EntityInterface $object
     * @param string          $mappingInfo
     * @param mixed           $value
     * @param array           $data
     */
    protected function populateUsingMapper(EntityInterface $object, $mappingInfo, $value, array $data = array())
    {
        // Creating the method name using the first element in the mapping info array
        $methodName = $this->createSetterNameFromPropertyName($mappingInfo[0]);

        // Populating the information using the mapper given in the mapping info array
        if (is_callable(array($object, $methodName))) {
            $mapper         = $this->mappers[$mappingInfo[1]];
            $dataToPopulate = $data;

            // When the $value is not an array it probably means that the data
            // is mixed in a huge array and certain mappers handle certain data
            if (is_array($value)) {

                // We change the data to be populated to avoid creating another condition
                $dataToPopulate = $value;

                if (is_array(current($dataToPopulate))) {

                    // Populating the object with the objects created from the arrays
                    foreach ($dataToPopulate as $newData) {
                        $object->$methodName($mapper->populate($newData));
                    }

                    // Resetting the data to we won't populate it one more times
                    $dataToPopulate = array();
                }
            }

            if(!empty($dataToPopulate)) {
                $object->$methodName($mapper->populate($dataToPopulate));
            }
        }
    }

    /**
     * @param mixed $data
     * @throws \RuntimeException
     * @throws WrongDataTypeException
     * @return EntityInterface
     */
    public function populate($data)
    {
        if (!is_array($data) && $data instanceof \ArrayIterator === false) {
            $message = 'The $data argument must be either an array or an instance of \ArrayIterator';
            $message .= gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        if (empty($this->entityClass)) {
            throw new \RuntimeException('The class for the entity has not been set');
        }

        $object = $this->createEntityObject();

        // Populating the object
        foreach ($data as $key => $value) {
            if (isset($this->map[$key])) {
                $propertyName = $this->map[$key];
                if (is_string($propertyName)) {
                    $this->populateUsingString($object, $propertyName, $value);
                } elseif (is_array($propertyName) && isset($this->mappers[$propertyName[1]])) {
                    $this->populateUsingMapper($object, $propertyName, $value, $data);
                }
            }
        }

        return $object;
    }

    /**
     * Returns a mapper matching the requested class
     * IMPORTANT: this feature will not look for a specific instance, just a class name
     *
     * @param $mapperClass
     * @return AbstractMapper|null
     */
    public function getMapper($mapperClass)
    {
        $foundMapper = null;

        if ($this->hasMapper($mapperClass)) {
            $foundMapper = $this->mappers[$mapperClass];
        } else {
            // Asking around in the other mappers for the requested mapper
            /** @var $mapper \Library\Model\Mapper\AbstractMapper */
            foreach ($this->mappers as $mapper) {
                $tmpMapper = $mapper->getMapper($mapperClass);
                if ($tmpMapper instanceof AbstractMapper) {
                    $foundMapper = $tmpMapper;
                    break;
                }
            }
        }

        return $foundMapper;
    }

    /**
     * @param $mapperClass
     * @return bool
     */
    public function hasMapper($mapperClass)
    {
        if (isset($this->mappers[$mapperClass])) {
            return true;
        }

        return false;
    }

    /**
     * @param AbstractMapper $mapper
     * @return MapperInterface
     */
    public function attachMapper(AbstractMapper $mapper)
    {
        $this->mappers[get_class($mapper)] = $mapper->setParentMapper($this);

        return $this;
    }

    /**
     * @param AbstractMapper $mapper
     * @return MapperInterface
     */
    public function setParentMapper(AbstractMapper $mapper)
    {
        $this->parentMapper = $mapper;

        return $this;
    }
}
