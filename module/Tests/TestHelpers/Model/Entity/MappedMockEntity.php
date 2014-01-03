<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model\Entity;

use Library\Model\Entity\AbstractMappedEntity;

class MappedMockEntity extends AbstractMappedEntity
{
    /**
     * @var string
     */
    protected $testField1 = '';

    /**
     * @var string
     */
    protected $testField2 = '';

    /**
     * @param string $testField1
     * @return MappedMockEntity
     */
    public function setTestField1($testField1)
    {
        $this->testField1 = $testField1;

        return $this;
    }

    /**
     * @return string
     */
    public function getTestField1()
    {
        return $this->testField1;
    }

    /**
     * @param string $testField2
     * @return MappedMockEntity
     */
    public function setTestField2($testField2)
    {
        $this->testField2 = $testField2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTestField2()
    {
        return $this->testField2;
    }
}
