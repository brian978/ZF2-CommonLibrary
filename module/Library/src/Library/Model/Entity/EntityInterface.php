<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Entity;

interface EntityInterface
{
    /**
     * @return array
     */
    public function getArrayCopy();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();
}
