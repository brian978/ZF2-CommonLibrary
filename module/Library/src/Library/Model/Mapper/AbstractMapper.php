<?php
/**
 * ZF2-CommonLibrary
 *
 * @author Andrei Dincescu <andrei.dincescu@ubisoft.com
 */

namespace Library\Model\Mapper;

use Library\Model\Entity\EntityInterface;

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
     * @param mixed           $value
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
     * @param string          $propertyName
     * @param mixed           $value
     * @param array           $data
     */
    protected function populateUsingMapper(EntityInterface $object, $propertyName, $value, array $data)
    {
        $methodName = $this->createSetterNameFromPropertyName($propertyName[0]);
        if (is_callable(array($object, $methodName))) {
            if (is_array($value)) {
                $object->$methodName($this->mappers[$propertyName[1]]->populate($value));
            } else {
                $object->$methodName($this->mappers[$propertyName[1]]->populate($data));
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

        $object = new $this->entityClass();

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
