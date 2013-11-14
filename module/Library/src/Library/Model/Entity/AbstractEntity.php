<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
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
        $objectProperties = array();

        // We need the method to be in the order they are declared (not reversed)
        $objectMethods = array_reverse($objectMethods);

        // Getting the names of the methods
        array_walk(
            $objectMethods,
            function ($value, $index) use ($objectMethods, &$objectProperties) {
                $methodName = $objectMethods[$index]->getName();
                if (strpos($methodName, 'get') === 0) {
                    $objectProperties[lcfirst(substr($methodName, 3))] = $methodName;
                }
                unset($value);
            }
        );

        // Getting the properties of the object
        foreach ($objectProperties as $propertyName => $methodName) {
            if ($propertyName !== 'mapper') {
                $properties[$propertyName] = $this->$propertyName;
            }
        }

        return $properties;
    }
}
