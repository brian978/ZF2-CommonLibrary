<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Db;

use Acamar\Model\Db\TableGateway as LibraryTableGateway;

class TableGateway extends LibraryTableGateway
{
    public function fetchJoined()
    {
        $processor = $this->getProcessorClone();
        $processor->getSelect()->join(
            'test2',
            'test.id = test2.testId',
            array(
                'joinedId' => 'id',
                'joinedField1' => 'field1',
                'joinedField2' => 'field2',
            )
        );

        return $processor;
    }
}
