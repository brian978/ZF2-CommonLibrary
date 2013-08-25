<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Model\Mapper;

use Tests\TestHelpers\Traits\AdapterTrait;
use Zend\Db\TableGateway\TableGateway;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    use AdapterTrait;

    public function testCanMapDataToObject()
    {
        $mock = $this->getMockBuilder('\Library\Model\Mapper\AbstractDbMapper')
            ->setConstructorArgs(array(new TableGateway('mapper', $this->getAdapter())))
            ->getMockForAbstractClass();

        $entityObject = $mock->setEntityClass('\Tests\TestHelpers\Model\Entity\MockEntity')
            ->setMap(array('entityId' => 'id'))
            ->populate(array('entityId' => 1));

        $this->assertEquals(1, $entityObject->getId());
    }
}
