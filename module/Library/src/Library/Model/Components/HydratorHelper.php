<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Components;

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
