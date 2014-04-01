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
use Library\Model\Entity\EntityInterface;
use Library\Model\Mapper\Exception\WrongDataTypeException;
use Zend\EventManager\EventManager;

class AbstractMapper implements MapperInterface
{
    // Notification codes
    const NOTIFY_BASE_CHANGED = 100;

    /**
     * @var MapCollection
     */
    protected $mapCollection = null;

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
     * @param string $property
     *
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
     * @param string $objectClass
     * @param EntityInterface $object
     * @param string $propertyName
     * @param string $value
     */
    protected function setProperty($objectClass, EntityInterface $object, $propertyName, $value)
    {
        if (!isset($this->callableMethods[$objectClass])) {
            $this->callableMethods[$objectClass] = [];
        }

        $callableMethods = & $this->callableMethods[$objectClass];

        // Calling the setter
        if (!isset($callableMethods[$propertyName])) {
            $methodName = $this->createSetterNameFromPropertyName($propertyName);
            if (is_callable(array($object, $methodName))) {
                $object->$methodName($value);
                $callableMethods[$propertyName] = $methodName;
            } else {
                $callableMethods[$propertyName] = false;
            }
        } else if ($callableMethods[$propertyName] !== false) {
            $object->{$callableMethods[$propertyName]}($value);
        }
    }

    /**
     * @param mixed $data
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

        // Default object value
        $object = null;

        // Selecting the map from the ones available
        $map = $this->findMap($mapName);

        if ($map !== null && isset($map['entity']) && isset($map['specs'])) {
            // Creating the object to populate
            $specs       = $map['specs'];
            $objectClass = $map['entity'];
            $object      = $this->createEntityObject($objectClass);

            // Populating the object
            foreach ($data as $key => $value) {
                if (isset($specs[$key])) {
                    $property = $specs[$key];
                    if (is_string($property)) {
                        $this->setProperty($objectClass, $object, $property, $value);
                    } elseif (is_array($property) && isset($property['toProperty']) && isset($property['map'])) {
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

        return $object;
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
