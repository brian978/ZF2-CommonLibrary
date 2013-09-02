<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Traits;

use Zend\Db\Adapter\Adapter;

trait DatabaseCreator
{
    /**
     * @var string
     */
    protected static $sqlitePaths = 'module/Tests/database';

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected static $adapter;

    public static function setUpBeforeClass()
    {
        static::createDb();
    }

    public static function tearDownAfterClass()
    {
        self::destroyDb();
    }

    protected static function createDb()
    {
        $databaseFilePath = self::$sqlitePaths . '/database_mapper.db';

        if (is_file($databaseFilePath)) {
            @unlink($databaseFilePath);
        }

        // Setting up the adapter
        self::$adapter = new Adapter(array(
            'driver' => 'Pdo_Sqlite',
            'database' => $databaseFilePath
        ));

        self::$adapter->query(
            file_get_contents(self::$sqlitePaths . '/schema.sqlite.sql'),
            Adapter::QUERY_MODE_EXECUTE
        );
        self::$adapter->query(file_get_contents(self::$sqlitePaths . '/data.sqlite.sql'), Adapter::QUERY_MODE_EXECUTE);
    }

    protected static function destroyDb()
    {
        // Removing the test database (connection to db needs to be closed)
        self::$adapter->getDriver()->getConnection()->disconnect();
        unlink(self::$sqlitePaths . '/database_mapper.db');
    }
}
