<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Model;

use PHPUnit_Framework_TestCase;
use Tests\TestHelpers\Model\DbHydratedModel;

class HydrateModelTest extends PHPUnit_Framework_TestCase
{
    public function testModelReturnsHydratedResult()
    {
        $model = new DbHydratedModel();
        $data  = current($model->fetch());

        $this->assertInstanceOf('\ArrayObject', $data);
    }
}
