<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Entity;

abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The method returns an array of properties that can be accessed via getters
     *
     * @return array
     */
    public function toArray()
    {
        $properties       = array();
        $reflectionClass  = new \ReflectionClass($this);
        $objectMethods    = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $objectProperties = $reflectionClass->getProperties(
            \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED
        );

        // Getting the names of the methods
        array_walk(
            $objectMethods,
            function (&$value, $index) use ($objectMethods) {
                $value = $objectMethods[$index]->getName();
            }
        );

        // Getting the properties of the object
        foreach ($objectProperties as $property) {
            $propertyName = $property->getName();

            // Making sure we have a getter for that property to avoid returning
            // values that should not be returned
            if (in_array('get' . ucfirst($propertyName), $objectMethods)) {
                $properties[$property->getName()] = $this->{$property->getName()};
            }
        }

        return $properties;
    }
}
