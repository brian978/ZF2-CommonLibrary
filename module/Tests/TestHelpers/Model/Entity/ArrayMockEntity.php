<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model\Entity;

class ArrayMockEntity extends MockEntity
{
    /**
     * @var string
     */
    protected $testField2 = array();

    /**
     * @param string $testField2
     * @return MockEntity
     */
    public function setTestField2($testField2)
    {
        $this->testField2[] = $testField2;

        return $this;
    }
}
