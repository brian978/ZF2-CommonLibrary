<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Collection;

use Library\Model\Db\Collection\GatewayTracker;
use Library\Model\Db\TableGateway;
use Tests\TestHelpers\AbstractTest;
use Tests\TestHelpers\Traits\DatabaseCreator;

class GatewayTrackerTest extends AbstractTest
{
    use DatabaseCreator;

    public function testCanTrackMultipleTablesAndReturnObjectBasedOnTableName()
    {
        // Creating the tables
        $table1 = new TableGateway(self::$adapter, 'test1');
        $table2 = new TableGateway(self::$adapter, 'test2');
        $table3 = new TableGateway(self::$adapter, 'test3');

        // Tracking the tables
        $tracker = new GatewayTracker();
        $tracker->track($table1);
        $tracker->track($table2);
        $tracker->track($table3);

        $this->assertEquals($table1, $tracker->getGateway('test1'));
    }
}
