<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
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
     * @param AbstractDbMapper $mapper
     * @return MapperInterface
     */
    public function attachMapper(AbstractDbMapper $mapper);

    /**
     * @param AbstractMapper $mapper
     * @return MapperInterface
     */
    public function setParentMapper(AbstractMapper $mapper);
}
