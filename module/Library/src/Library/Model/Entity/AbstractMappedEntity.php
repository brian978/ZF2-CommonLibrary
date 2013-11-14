<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Entity;

use Library\Model\Mapper\MapperInterface;

class AbstractMappedEntity extends AbstractEntity
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @param \Library\Model\Mapper\MapperInterface $mapper
     *
     * @return AbstractMappedEntity
     */
    public function setMapper($mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * @return \Library\Model\Mapper\MapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }
}
