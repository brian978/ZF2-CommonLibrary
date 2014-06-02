<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper;

use Library\Model\Entity\AbstractMappedEntity;
use Library\Model\Entity\EntityCollection;
use Library\Model\Entity\EntityInterface;
use Library\Model\Mapper\Exception\WrongDataTypeException;
use Zend\EventManager\EventManager;

class AbstractMapper implements MapperInterface
{
    /**
     * @var MapCollection
     */
    protected $mapCollection = null;

    /**
     * @var \Library\Model\Entity\EntityCollection
     */
    protected $collectionPrototype = null;

    /**
     * This is sort of a cache to avoid creating new objects each time
     *
     * @var array
     */
    protected $entityPrototypes = array();

    /**
     * @var array
     */
    protected $callableMethods = array();

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @param MapCollection $mapCollection
     */
    public function __construct(MapCollection $mapCollection)
    {
        $this->mapCollection = $mapCollection;

        // Initializing the entity collection prototype
        $this->collectionPrototype = new EntityCollection();
    }

    /**
     * @param string $name
     * @return array
     */
    public function findMap($name)
    {
        return $this->mapCollection->findMap($name);
    }

    /**
     * @param string $className
     * @throws \RuntimeException
     * @return EntityInterface|AbstractMappedEntity
     */
    public function createEntityObject($className)
    {
        /** @var $entity EntityInterface */
        if (!isset($this->entityPrototypes[$className])) {
            $entity = new $className();
            if ($entity instanceof EntityInterface) {
                $this->entityPrototypes[$className] = $entity;
            } else {
                throw new \RuntimeException(
                    'The class for the entity must implement \Library\Model\Entity\EntityInterface'
                );
            }
        }

        return clone $this->entityPrototypes[$className];
    }

    /**
     *
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
     * Populates a property of an object
     *
     * @param string $objectClass
     * @param EntityInterface $object
     * @param string $propertyName
     * @param string|EntityInterface $value
     */
    protected function setProperty($objectClass, EntityInterface $object, $propertyName, $value)
    {
        if (!isset($this->callableMethods[$objectClass])) {
            $this->callableMethods[$objectClass] = [];
        }

        $callableMethods = & $this->callableMethods[$objectClass];

        // Calling the setter and registering it in the callableMethods property for future use
        if (!isset($callableMethods[$propertyName])) {
            $methodName = $this->createSetterNameFromPropertyName($propertyName);
            if (is_callable(array($object, $methodName))) {
                // We need to determine if we have a hinted parameter (for a collection)
                $reflection           = new \ReflectionObject($object);
                $reflectionMethod     = $reflection->getMethod($methodName);
                $reflectionParameters = $reflectionMethod->getParameters();
                $reflectionClass      = $reflectionParameters[0]->getClass();

                // Checking if we have to insert a collection into the object's property
                $collection = null;
                if ($reflectionClass instanceof \ReflectionClass
                    && $reflectionClass->isInstance($this->collectionPrototype)
                ) {
                    $collection = $reflectionClass->newInstance();
                }

                // Populating the property accordingly
                if ($collection !== null) {
                    $collection->add($value);
                    $object->$methodName($collection);
                } else {
                    $object->$methodName($value);
                }

                // Caching our info so it's faster next time
                $callableMethods[$propertyName] = array("method" => $methodName, "collection" => $collection);
            } else {
                // We set this to false so we don't create the setter name again next time
                $callableMethods[$propertyName] = false;
            }
        } else if ($callableMethods[$propertyName] !== false) {
            if ($callableMethods[$propertyName]["collection"] !== null) {
                /** @var $collection EntityCollection */
                $collection = $callableMethods[$propertyName]["collection"];
                $collection->add($value);
            } else {
                $object->{$callableMethods[$propertyName]["method"]}($value);
            }
        }
    }

    /**
     * @param mixed $data
     * @param string|array $map
     * @param \Library\Model\Entity\EntityInterface $object
     * @throws Exception\WrongDataTypeException
     * @return EntityInterface
     */
    public function populate($data, $map = 'default', EntityInterface $object = null)
    {
        if (!is_array($data) && $data instanceof \ArrayIterator === false) {
            $message = 'The $data argument must be either an array or an instance of \ArrayIterator';
            $message .= gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        // Selecting the map from the ones available
        if (is_string($map)) {
            $map = $this->findMap($map);
        } else if (!is_array($map)) {
            $map = null;
        }

        if ($map !== null && isset($map['entity']) && isset($map['specs'])) {
            // Creating the object to populate
            $specs       = $map['specs'];
            $objectClass = $map['entity'];

            // We might get an object to populate
            if ($object !== null) {
                $object = $this->createEntityObject($objectClass);
            }

            // Populating the object
            foreach ($data as $key => $value) {
                if (isset($specs[$key])) {
                    $property = $specs[$key];
                    if (is_string($property)) {
                        $this->setProperty($objectClass, $object, $property, $value);
                    } elseif (is_array($property) && isset($property['toProperty']) && isset($property['map'])) {
                        // This might be a collection of objects so we need to check that first
                        if (isset($this->callableMethods[$objectClass][$property['toProperty']])
                            && $this->callableMethods[$objectClass][$property['toProperty']]['collection'] !== null
                        ) {
                            // Locate sub-object and send the population that to it
                            // (we will most likely need to attach another object to that one)
                        } else {
                            $this->setProperty(
                                $objectClass,
                                $object,
                                $property['toProperty'],
                                $this->populate($data, $property['map'])
                            );
                        }
                    }
                }
            }
        }

        return $object;
    }

    /**
     * @param array $data
     * @param string $mapName
     * @return EntityCollection
     * @throws Exception\WrongDataTypeException
     */
    public function populateCollection($data, $mapName = "default")
    {
        if (!is_array($data) && $data instanceof \ArrayIterator === false) {
            $message = 'The $data argument must be either an array or an instance of \ArrayIterator';
            $message .= gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        $map        = $this->findMap($mapName);
        $collection = clone $this->collectionPrototype;

        foreach ($data as $part) {
            // TODO: check if this can be done better (not repeated or more optimized)
            // Searching for an already populated object (if any)
            $object = null;
            if ($map !== null && $collection->count() > 0 && isset($map['identProperty'])) {
                $object = $this->locateInCollection($collection, $map, $part);
            }

            // Populating the object
            if ($object) {
                $this->populate($part, $map, $object);
            } else {
                $collection->add($this->populate($part, $map));
            }
        }

        return $collection;
    }

    /**
     * @param EntityCollection $collection
     * @param array $map
     * @param array $data
     * @return null|EntityInterface
     */
    protected function locateInCollection($collection, $map, $data)
    {
        // Getting the data that will help us identify the object in the collection
        $idData = null;
        if (isset($map['identProperty'])) {
            $specs = $map['specs'];
            foreach ($specs as $dataIndex => $propertyName) {
                if ($map['identProperty'] == $propertyName) {
                    $idData = $data[$dataIndex];
                    break;
                }
            }
        }

        // TODO: optimize this
        // Locating the object in the collection
        if ($idData !== null) {
            $methodName = $this->createSetterNameFromPropertyName($map['identProperty']);
            foreach ($collection as $object) {
                if ($object->$methodName() == $idData) {
                    return $object;
                }
            }
        }

        return null;
    }

    /**
     * @param EntityCollection $collection
     * @param string $mapName
     * @return array
     */
    public function extractCollection(EntityCollection $collection, $mapName = 'default')
    {
        $result = array();
        foreach ($collection as $object) {
            $result[] = $this->extract($object, $mapName);
        }

        return $result;
    }

    /**
     * @param \Library\Model\Entity\EntityInterface $object
     * @param string $mapName
     * @return array
     */
    public function extract(EntityInterface $object, $mapName = 'default')
    {
        $result = array();

        // Selecting the map from the ones available
        $map = $this->findMap($mapName);

        // No need to continue if we have no map
        if ($map === null || !isset($map['specs'])) {
            return $result;
        }

        // We need to flip the values and the field names in the map because
        // we need to do the reverse operation of the populate
        $reversedMap = $this->mapCollection->flip($map);

        // Extracting the first layer of the results
        $tmpResult = $object->toArray();

        // Creating the result
        foreach ($tmpResult as $field => $value) {
            if ($value instanceof EntityInterface) {
                $extracted = $this->extract($value, $this->findMapForField($field, $map));
                $result    = array_merge($result, $extracted);
            } else {
                // We only need to extract the fields that are in the map
                // (the populate() method does the exact thing - only sets data that is in the map)
                if (isset($reversedMap[$field])) {
                    $result[$reversedMap[$field]] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @param array $map
     * @return string
     */
    protected function findMapForField($fieldName, $map)
    {
        foreach ($map['specs'] as $toSpecs) {
            if (is_array($toSpecs) && $toSpecs['toProperty'] === $fieldName) {
                return $toSpecs['map'];
            }
        }

        return '';
    }
}
