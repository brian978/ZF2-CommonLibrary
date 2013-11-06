<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Mapper;

class Map
{
    /**
     * @var string
     */
    protected $name = 'default';

    /**
     * @param string $name
     */
    public function __construct($name = '')
    {
        if (!empty($name) && is_string($name)) {
            $this->setName($name);
        }
    }

    /**
     * @param string $name
     * @return Map
     */
    public function setName($name)
    {
        if (is_string($name)) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
