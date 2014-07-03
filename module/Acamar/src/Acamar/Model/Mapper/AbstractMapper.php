<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Model\Mapper;

use Acamar\Model\Entity\EntityCollection;
use Acamar\Model\Entity\EntityCollectionInterface;
use Acamar\Model\Entity\EntityInterface;
use Acamar\Model\Mapper\Exception\WrongDataTypeException;

class AbstractMapper implements MapperInterface
{
    /**
     * @var MapCollection
     */
    protected $mapCollection = null;

    /**
     * @var \Acamar\Model\Entity\EntityCollectionInterface
     */
    protected $collectionPrototype = null;

    /**
     * This is sort of a cache to avoid creating new objects each time
     *
     * @var array
     */
    protected $entityPrototypes = array();

    /**
     * The callable methods property acts like a cache for the setProperty() method
     *
     * @var array
     */
    protected $callableMethods = array();

    /**
     * @param MapCollection $mapCollection
     */
    public function __construct(MapCollection $mapCollection)
    {
        $this->mapCollection = $mapCollection;
    }

    /**
     *
     * @param EntityCollectionInterface $collectionPrototype
     * @return $this
     */
    public function setCollectionPrototype(EntityCollectionInterface $collectionPrototype)
    {
        $this->collectionPrototype = $collectionPrototype;

        return $this;
    }

    /**
     *
     * @return EntityCollection
     */
    public function getCollectionPrototype()
    {
        if ($this->collectionPrototype === null) {
            $this->collectionPrototype = new EntityCollection();
        }

        return $this->collectionPrototype;
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
     * @return EntityInterface
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
                    'The class for the entity must implement \Acamar\Model\Entity\EntityInterface'
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
     *
     * @param string $property
     * @return mixed
     */
    protected function createGetterNameFromPropertyName($property)
    {
        return preg_replace_callback(
            '/_([a-z])/',
            function ($string) {
                return ucfirst($string);
            },
            'get' . ucfirst($property)
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
                $collectionPrototype = null;
                if ($reflectionClass instanceof \ReflectionClass
                    && $reflectionClass->isInstance($this->getCollectionPrototype())
                ) {
                    $collectionPrototype = $reflectionClass->newInstance();
                }

                // Populating the property accordingly
                if ($collectionPrototype !== null) {
                    $collection = clone $collectionPrototype;
                    $collection->add($value);
                    $object->$methodName($collection);
                } else {
                    $object->$methodName($value);
                }

                // Caching our info so it's faster next time
                $callableMethods[$propertyName] = array("method" => $methodName, "collection" => $collectionPrototype);
            } else {
                // We set this to false so we don't create the setter name again next time
                $callableMethods[$propertyName] = false;
            }
        } else if ($callableMethods[$propertyName] !== false) {
            $methodName = $callableMethods[$propertyName]["method"];

            // Getting the collection prototype from cache
            $collectionPrototype = null;
            if ($callableMethods[$propertyName]["collection"] !== null) {
                /** @var $collection EntityCollectionInterface */
                $collectionPrototype = $callableMethods[$propertyName]["collection"];
            }

            // Populating the property accordingly
            if ($collectionPrototype !== null) {
                $collection = clone $collectionPrototype;
                $collection->add($value);
                $object->$methodName($collection);
            } else {
                $object->$methodName($value);
            }
        }
    }

    /**
     * @param array|\ArrayIterator $data
     * @param string $mapName
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

        $object = null;
        $map    = $this->findMap($mapName);

        if ($map !== null && isset($map['entity']) && isset($map['specs'])) {
            // Creating the object to populate
            $identField  = (isset($map['identField']) ? $map['identField'] : null);
            $specs       = $map['specs'];
            $objectClass = $map['entity'];

            if ($identField === null || (isset($data[$identField]) && $data[$identField] !== null)) {
                // We don't need to create the object if we can't identify it
                $object = $this->createEntityObject($objectClass);

                // Populating the object
                foreach ($data as $key => $value) {
                    if (isset($specs[$key])) {
                        $property = $specs[$key];
                        if (is_string($property)) {
                            $this->setProperty($objectClass, $object, $property, $value);
                        } elseif (is_array($property) && isset($property['toProperty']) && isset($property['map'])) {
                            $childObject = $this->populate($data, $property['map']);
                            if ($childObject !== null) {
                                $this->setProperty($objectClass, $object, $property['toProperty'], $childObject);
                            }
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
     * @param EntityCollectionInterface $collection
     * @return EntityCollectionInterface
     * @throws Exception\WrongDataTypeException
     */
    public function populateCollection($data, $mapName = "default", EntityCollectionInterface $collection = null)
    {
        if (!is_array($data) && $data instanceof \ArrayIterator === false) {
            $message = 'The $data argument must be either an array or an instance of \ArrayIterator';
            $message .= gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        $map = $this->findMap($mapName);

        if ($collection === null) {
            $collection = clone $this->getCollectionPrototype();
        }

        foreach ($data as $part) {
            // Locating the main object (if there is one)
            $object = null;
            if ($map !== null && $collection->count() > 0 && isset($map['identField'])) {
                $object = $this->locateInCollection($collection, $map, $part);
            }

            if ($object) {
                // Locating the collections in the objects to pass the data to them as well
                $specs = $map['specs'];
                foreach ($specs as $propertyName) {
                    if (is_array($propertyName) && isset($propertyName['toProperty']) && isset($propertyName['map'])) {
                        $methodName = $this->createGetterNameFromPropertyName($propertyName['toProperty']);
                        if ($object->$methodName() instanceof EntityCollectionInterface) {
                            $this->populateCollection(array($part), $propertyName['map'], $object->$methodName());
                        }
                    }
                }
            } else {
                $collection->add($this->populate($part, $mapName));
            }
        }

        return $collection;
    }

    /**
     * @param EntityCollectionInterface $collection
     * @param array $map
     * @param array $data
     * @return null|EntityInterface
     */
    protected function locateInCollection(EntityCollectionInterface $collection, $map, $data)
    {
        $specs = $map['specs'];

        // Getting the data that will help us identify the object in the collection
        $idData       = null;
        $propertyName = "";
        if (isset($map['identField'])) {
            foreach ($specs as $dataIndex => $propertyName) {
                if (is_string($propertyName) && strcmp($map['identField'], $dataIndex) === 0) {
                    $idData = $data[$dataIndex];
                    break;
                }
            }
        }

        // TODO: optimize this
        // Locating the object in the collection
        if ($idData !== null) {
            $methodName = $this->createGetterNameFromPropertyName($propertyName);
            foreach ($collection as $object) {
                if ($object->$methodName() == $idData) {
                    return $object;
                }
            }
        }

        return null;
    }

    /**
     * @param \Acamar\Model\Entity\EntityInterface $object
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
     *
     * @param EntityCollectionInterface $collection
     * @param string $mapName
     * @return array
     */
    public function extractCollection(EntityCollectionInterface $collection, $mapName = 'default')
    {
        $result = array();

        // Selecting the map from the ones available
        $map = $this->findMap($mapName);

        // No need to continue if we have no map
        if ($map === null || !isset($map['specs'])) {
            return $result;
        }

        foreach ($collection as $object) {
            $collectionData = [];

            // The object data will contain child object but will ignore collections
            $objectData = $this->extract($object, $mapName);

            // Locating the potential collections in the current object since what we extracted
            // will not contain them
            foreach ($map['specs'] as $toField) {
                if (is_array($toField)) {
                    $methodName    = $this->createGetterNameFromPropertyName($toField['toProperty']);
                    $propertyValue = $object->$methodName();
                    if ($propertyValue instanceof EntityCollectionInterface) {
                        $collectionData = $this->extractCollection(
                            $propertyValue,
                            $toField['map'],
                            $objectData
                        );
                    }
                }
            }

            if (count($collectionData) > 0) {
                foreach ($collectionData as $childObjData) {
                    $result[] = array_merge($objectData, $childObjData);
                }
            } else {
                $result[] = $objectData;
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
