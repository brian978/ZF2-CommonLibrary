<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model\Entity;

use Library\Model\Entity\AbstractEntity;
use Library\Model\Entity\EntityCollection;

class CEntity1 extends AbstractEntity
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $name = "";

    /**
     * @var \Library\Model\Entity\EntityCollection
     */
    protected $cEntity2 = null;

    /**
     * @param int $id
     *
     * @return CEntity1
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return CEntity1
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @param \Library\Model\Entity\EntityCollection $cEntity2
     *
     * @return CEntity1
     */
    public function setCEntity2(EntityCollection $cEntity2)
    {
        $this->cEntity2 = $cEntity2;

        return $this;
    }

    /**
     * @return \Library\Model\Entity\EntityCollection
     */
    public function getCEntity2()
    {
        return $this->cEntity2;
    }
}
