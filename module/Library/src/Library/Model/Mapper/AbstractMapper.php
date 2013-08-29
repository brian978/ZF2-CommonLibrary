<?php
/**
 * ZF2-CommonLibrary
 *
 * @author Andrei Dincescu <andrei.dincescu@ubisoft.com
 */

namespace Library\Model\Mapper;

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
     * The map is immutable (mostly)
     *
     * @param array $map
     * @param bool  $force [ optional ] Should used only as a last resort
     * @return AbstractDbMapper
     */
    public function setMap(array $map, $force = false)
    {
        if (empty($this->map) || $force === true) {
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
     * @param mixed $data
     * @throws \RuntimeException
     * @throws WrongDataTypeException
     * @return \Library\Model\Entity\EntityInterface
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

                // Populating the object either with the given value or with another object from another mapper
                if (is_string($propertyName)) {
                    $methodName = $this->createSetterNameFromPropertyName($propertyName);
                    if (is_callable(array($object, $methodName))) {
                        $object->$methodName($value);
                    }
                } elseif (is_array($propertyName) && isset($this->mappers[$propertyName[1]])) {
                    $methodName = $this->createSetterNameFromPropertyName($propertyName[0]);
                    if (is_callable(array($object, $methodName))) {
                        $object->$methodName($this->mappers[$propertyName[1]]->populate($data));
                    }
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
