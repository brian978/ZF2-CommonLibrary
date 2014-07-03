<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Model\Entity;

use Acamar\Model\Mapper\MapperInterface;

class AbstractMappedEntity extends AbstractEntity
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @param \Acamar\Model\Mapper\MapperInterface $mapper
     *
     * @return AbstractMappedEntity
     */
    public function setMapper($mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * @return \Acamar\Model\Mapper\MapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }
}
