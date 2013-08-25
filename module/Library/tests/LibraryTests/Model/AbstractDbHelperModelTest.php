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
use Tests\TestHelpers\Model\DbHelperModel;
use Tests\TestHelpers\Model\DbModel;
use Tests\TestHelpers\Traits\AdapterTrait;
use Zend\Log\Logger;

class AbstractDbHelperModelTest extends PHPUnit_Framework_TestCase
{
    use AdapterTrait;

    /**
     * Creates the required mocks (don't need to create/re-create them for every test)
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // Creating the mocks
        $this->getAdapter();
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

    public function testIfSqlIsLoaded()
    {
        $dbHelper = new DbHelperModel($this->adapter);

        $this->assertInstanceOf('Zend\Db\Sql\Sql', $dbHelper->getSql());
    }

    public function testModelHasDefaultLogger()
    {
        $dbModel = new DbModel();

        $this->assertInstanceOf('\Library\Log\DummyLogger', $dbModel->getLogger());
    }

    public function testModelHasLogger()
    {
        $dbModel = new DbModel();
        $dbModel->setLogger(new Logger());

        $this->assertInstanceOf('\Zend\Log\Logger', $dbModel->getLogger());
    }
}
