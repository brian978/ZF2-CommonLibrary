<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTests\Model\Entity;

use PHPUnit_Framework_TestCase;
use Tests\TestHelpers\Model\Entity\MappedMockEntity;

class EntityTest extends PHPUnit_Framework_TestCase
{
    public function testIdSetter()
    {
        $mock = $this->getMockBuilder('\Acamar\Model\Entity\AbstractEntity')
            ->getMockForAbstractClass()
            ->setId(1);

        $this->assertEquals(1, $mock->getId());
    }

    public function testEntityCanOutputArray()
    {
        $mock = new MappedMockEntity();
        $mock->setId(1);

        $this->assertEquals(array('id' => 1, 'testField1' => '', 'testField2' => ''), $mock->toArray());
    }
}
