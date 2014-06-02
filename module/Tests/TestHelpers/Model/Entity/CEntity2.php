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

class CEntity2 extends AbstractEntity
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
     * @var int
     */
    protected $typeId = 0;

    /**
     * @var EntityCollection
     */
    protected $cEntity3 = null;

    /**
     * @param int $id
     *
     * @return CEntity2
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
     * @return CEntity2
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
     * @param int $typeId
     *
     * @return CEntity2
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param \Library\Model\Entity\EntityCollection $cEntity3
     *
     * @return CEntity2
     */
    public function setCEntity3(EntityCollection $cEntity3)
    {
        $this->cEntity3 = $cEntity3;

        return $this;
    }

    /**
     * @return \Library\Model\Entity\EntityCollection
     */
    public function getCEntity3()
    {
        return $this->cEntity3;
    }
}
