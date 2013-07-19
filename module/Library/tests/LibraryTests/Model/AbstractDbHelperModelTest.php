<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Model;

use PHPUnit_Framework_TestCase;
use TestHelpers\Model\DbHelperModel;

class AbstractDbHelperModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * Creates the required mocks (don't need to create/re-create them for every test)
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // Creating the mocks
        $adapter = $this->getMockBuilder('\Zend\Db\Adapter\AdapterInterface')
            ->getMock();

        $platform = $this->getMockBuilder('\Zend\Db\Adapter\Platform\PlatformInterface')
            ->getMock();

        $driver = $this->getMockBuilder('\Zend\Db\Adapter\Driver\DriverInterface')
            ->getMock();

        $connection = $this->getMockBuilder('\Zend\Db\Adapter\Driver\DriverInterface')
            ->getMock();

        // Building the adapter mock
        $adapter->expects($this->any())
            ->method('getDriver')
            ->will($this->returnValue($driver));

        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platform));

        // Building the platform mock
        $platform->expects($this->any())
            ->method('quoteIdentifierChain')
            ->will($this->returnCallback(function ($identifierChain) {
                    return implode('.', $identifierChain);
                }
            ));

        $platform->expects($this->any())
            ->method('quoteValue')
            ->will($this->returnArgument(0));

        // Building the driver mock
        $driver->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        $this->adapter = $adapter;
    }

    /**
     * Used by the platform mock
     *
     * @param $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain)
    {
        return implode('.', $identifierChain);
    }

    public function testBuildWhereString()
    {
        $dbHelper = new DbHelperModel($this->adapter);

        $this->assertEquals('foo.bar=12', $dbHelper->buildWhere('bar', '12', 'foo'));
    }

    public function testIfWhereArrayIsProperlyPopulated()
    {
        $dbHelper = new DbHelperModel($this->adapter);

        $dbHelper->addWhere('bar', '12', 'foo');
        $dbHelper->addWhere('bar1', '13', 'foo');
        $dbHelper->addWhere('bar2', '14', 'foo');

        $this->assertEquals(array(
            'foo.bar=12',
            'foo.bar1=13',
            'foo.bar2=14',
        ), $dbHelper->getWhere());
    }

    public function testIfWhereArrayIsPopulatedWithAnArray()
    {
        $dbHelper = new DbHelperModel($this->adapter);

        $dbHelper->addWhere('bar', '12', 'foo');
        $dbHelper->addWhere('bar1', '13', 'foo');
        $dbHelper->addWhere('bar2', '14', 'foo');

        $dbHelper->addWhere(array(
            $dbHelper->buildWhere('bar3', '15', 'foo')
        ), true);

        $this->assertEquals(array(
            'foo.bar=12',
            'foo.bar1=13',
            'foo.bar2=14',
            'foo.bar3=15',
        ), $dbHelper->getWhere());
    }

    public function testIfWhereArrayIsPopulatedWithAString()
    {
        $dbHelper = new DbHelperModel($this->adapter);

        $dbHelper->addWhere($dbHelper->buildWhere('bar3', '15', 'foo'), true);

        $this->assertEquals(array(
            'foo.bar3=15',
        ), $dbHelper->getWhere());
    }
}