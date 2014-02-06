<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Model\Mapper;


use Library\Model\Mapper\Map;
use Tests\TestHelpers\AbstractTest;

class MapTest extends AbstractTest
{
    public function testCanCreateMapWithSpecs()
    {
        $specs = array(
            "id" => "id",
            "someName" => "name"
        );

        $map = new Map("default", $specs);

        $this->assertEquals("default", $map->getName());
        $this->assertEquals($specs, $map->getSpecs());
    }

    public function testMapCanActAsArray()
    {
        $this->assertInstanceOf('\ArrayAccess', new Map());
    }

    public function testMapCanBeIterated()
    {
        $specs = array(
            "id" => "id",
            "someName" => "name"
        );

        $map = new Map("default", $specs);

        $newSpecs = array();
        foreach($map as $field => $name) {
            $newSpecs[$field] = $name;
        }

        $this->assertEquals($specs, $newSpecs);
    }
}
