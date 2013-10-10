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
use Library\Model\Mapper\Exception\MapperNotFoundException;
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
     * @see AbstractMapperMap.phps
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
     * @var AbstractMapper
     */
    protected $baseMapper = null;

    /**
     * The array holds the associations between a method and a property
     * It is used for both the setters and the getters
     *
     * @var array
     */
    protected $propertyToMethod = array();

    /**
     * @param array $map
     *
     * @return AbstractMapper
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
     *
     * @return AbstractMapper
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
     * @throws \RuntimeException
     * @return \Library\Model\Entity\AbstractEntity
     */
    public function createEntityObject()
    {
        if (empty($this->entityClass)) {
            throw new \RuntimeException('The class for the entity has not been set');
        }

        return new $this->entityClass();
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    protected function createSetterNameFromPropertyName($property)
    {
        // Creating the association
        if (!isset($this->propertyToMethod['setter'][$property])) {
            $this->propertyToMethod['setter'][$property] = preg_replace_callback(
                '/_([a-z])/',
                function ($string) {
                    return ucfirst($string);
                },
                'set' . ucfirst($property)
            );
        }

        return $this->propertyToMethod['setter'][$property];
    }

    /**
     * The $propertyName are the options for a specific field
     *
     * @param $mappingInfo
     *
     * @internal param $propertyName
     * @return bool
     */
    protected function useMapper(array $mappingInfo)
    {
        $result = false;

        if (isset($mappingInfo['mapper'][1])) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param array $mappingInfo
     *
     * @throws \RuntimeException
     * @return mixed
     */
    protected function getMethodNameFromInfo(array $mappingInfo)
    {
        if (!isset($mappingInfo['mapper'][0])) {
            throw new \RuntimeException('Method name not found in mapper section from the map');
        }

        return $mappingInfo['mapper'][0];
    }

    /**
     * @param array $mappingInfo
     *
     * @throws \RuntimeException
     * @throws Exception\MapperNotFoundException
     * @return AbstractMapper
     */
    protected function getMapperFromInfo(array $mappingInfo)
    {
        if (!isset($mappingInfo['mapper'][1])) {
            throw new \RuntimeException('Mapper class not found in mapper section from the map');
        }

        $mapperClass = $mappingInfo['mapper'][1];

        if (!isset($this->mappers[$mapperClass])) {
            throw new MapperNotFoundException('The mapper "' . $mapperClass . '" was not attached.');
        }

        return $this->mappers[$mapperClass];
    }

    /**
     * @param EntityInterface $object
     * @param string $propertyName
     * @param string $value
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
     * @param array $mappingInfo
     * @param mixed $value
     * @param array $data
     */
    protected function populateUsingMapper(EntityInterface $object, array $mappingInfo, $value, array $data = array())
    {
        // Creating the method name using the first element in the mapping info array
        $methodName = $this->createSetterNameFromPropertyName($this->getMethodNameFromInfo($mappingInfo));

        // Populating the information using the mapper given in the mapping info array
        if (is_callable(array($object, $methodName))) {
            $mapper         = $this->getMapperFromInfo($mappingInfo);
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

            if (!empty($dataToPopulate)) {
                $object->$methodName($mapper->populate($dataToPopulate));
            }
        }
    }

    /**
     * @param mixed $data
     * @param string $mapName
     *
     * @throws Exception\WrongDataTypeException
     * @return EntityInterface
     */
    public function populate($data, $mapName = 'default')
    {
        if (!is_array($data) && $data instanceof \ArrayIterator === false) {
            $message = 'The $data argument must be either an array or an instance of \ArrayIterator';
            $message .= gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        // Creating the object to use (may throw exception if no entity class is provided)
        $object = $this->createEntityObject();

        // Selecting the map
        if (isset($this->map[$mapName])) {
            $selectedMap = $this->map[$mapName];
        } else {
            $selectedMap = $this->map;
        }

        // Populating the object
        foreach ($data as $key => $value) {
            if (isset($selectedMap[$key])) {
                $propertyName = $selectedMap[$key];
                if (is_string($propertyName)) {
                    $this->populateUsingString($object, $propertyName, $value);
                } elseif (is_array($propertyName) && $this->useMapper($propertyName)) {
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
     *
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
     *
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
     *
     * @throws \RuntimeException
     * @return MapperInterface
     */
    public function attachMapper(AbstractMapper $mapper)
    {
        if ($mapper->getParentMapper() !== null && $mapper->getParentMapper() !== $this) {
            throw new \RuntimeException('Cannot attach the mapper because it already has a parent');
        }

        $this->mappers[get_class($mapper)] = $mapper->setParentMapper($this);

        return $this;
    }

    /**
     * @param AbstractMapper $mapper
     *
     * @return MapperInterface
     */
    public function setParentMapper(AbstractMapper $mapper)
    {
        $this->parentMapper = $mapper;

        return $this;
    }

    /**
     * @return \Library\Model\Mapper\AbstractMapper
     */
    public function getParentMapper()
    {
        return $this->parentMapper;
    }

    /**
     * Returns the top most mapper
     *
     * @return \Library\Model\Mapper\AbstractMapper
     */
    public function getBaseMapper()
    {
        if ($this->baseMapper === null) {
            $baseMapper = null;
            $parentMapper = $this->getParentMapper();

            while ($parentMapper !== null) {
                $baseMapper = $parentMapper;
                $parentMapper = $parentMapper->getParentMapper();
            }

            $this->baseMapper = $baseMapper;
        }

        return $this->baseMapper;
    }
}
