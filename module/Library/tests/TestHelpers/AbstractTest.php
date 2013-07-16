<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace TestHelpers;

use PHPUnit_Framework_TestCase;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

class AbstractTest extends PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->serviceManager = new ServiceManager(new ServiceManagerConfig());
        $this->serviceManager->setService('ApplicationConfig', ApplicationConfig::getConfig());
        $this->serviceManager->get('ModuleManager')->loadModules();
    }
}
