<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Model\Entity;

abstract class AbstractEntity implements EntityInterface
{
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
                /** @var $reflectionMethod \ReflectionMethod */
                $reflectionMethod = $objectMethods[$index];
                $methodName = $reflectionMethod->getName();
                if (strpos($methodName, 'get') === 0) {
                    $objectProperties[lcfirst(substr($methodName, 3))] = $methodName;
                }
                unset($value);
            }
        );

        // Getting the properties of the object
        foreach ($objectProperties as $propertyName => $methodName) {
            if ($propertyName !== 'mapper') {
                $properties[$propertyName] = $this->$methodName();
            }
        }

        return $properties;
    }
}
