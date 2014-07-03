<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Model\Components;

use Zend\Stdlib\Hydrator\ClassMethods;

trait HydratorHelper
{
    /**
     * @var ClassMethods
     */
    protected $hydrator = null;

    /**
     * @return ClassMethods
     */
    protected function getHydrator()
    {
        if ($this->hydrator === null) {
            $this->hydrator = new ClassMethods(false);
        }

        return $this->hydrator;
    }
}
