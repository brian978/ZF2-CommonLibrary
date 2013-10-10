<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper;

interface MapperInterface
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function populate($data);

    /**
     * @return \Library\Model\Entity\AbstractEntity
     */
    public function createEntityObject();

    /**
     * @param AbstractMapper $mapper
     * @return MapperInterface
     */
    public function attachMapper(AbstractMapper $mapper);

    /**
     * @param AbstractMapper $mapper
     * @return MapperInterface
     */
    public function setParentMapper(AbstractMapper $mapper);
}
