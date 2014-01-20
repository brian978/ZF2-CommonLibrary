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
     * Contains the specifications of the map (like what field to map to what field)
     *
     * This replaces the map in the mapper
     *
     * @var array
     */
    protected $specs = array();

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

    /**
     * @param array $specs
     * @return $this
     */
    public function setSpecs($specs)
    {
        $this->specs = $specs;

        return $this;
    }

    /**
     * @return array
     */
    public function getSpecs()
    {
        return $this->specs;
    }

    /**
     * Flips the map specs (but not permanently - the specs remain untouched)
     *
     * @return array
     */
    protected function flip()
    {
        $flipped = array();
        foreach ($this->specs as $fromField => $toField) {
            if (is_string($toField) || is_numeric($toField)) {
                $flipped[$toField] = $fromField;
            }
        }

        return $flipped;
    }
}
