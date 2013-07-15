<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Service;

use PHPUnit_Framework_TestCase;
use TestHelpers\ApplicationConfig;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

class DependencyAwareFormTest extends PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->serviceManager = new ServiceManager(new ServiceManagerConfig());
        $this->serviceManager->setService('ApplicationConfig', ApplicationConfig::getConfig());
        $this->serviceManager->get('ModuleManager')->loadModules();
    }

    public function testCanCreateDependencyAwareFormFactory()
    {
        /** @var $service \Library\Form\DependencyAwareFormFactory */
        $service = $this->serviceManager->get('DependencyAwareForm');

        $this->assertInstanceOf('\Library\Form\DependencyAwareFormFactory', $service);
    }

    public function testFormElementManagerHasServiceLocator()
    {
        /** @var $service \Library\Form\DependencyAwareFormFactory */
        $service = $this->serviceManager->get('DependencyAwareForm');

        $this->assertInstanceOf(
            '\Zend\ServiceManager\ServiceLocatorInterface',
            $service->getFormElementManager()->getServiceLocator()
        );
    }

    public function testCanCreateFormWithDependencies()
    {
        /** @var $service \Library\Form\DependencyAwareFormFactory */
        $service = $this->serviceManager->get('DependencyAwareForm');

        /** @var $form \TestHelpers\Form\AwareForm */
        $form = $service->createForm(array('type' => '\TestHelpers\Form\AwareForm'));

        $this->assertInstanceOf('\TestHelpers\Form\AwareForm', $form);
        $this->assertInstanceOf('\Zend\ServiceManager\ServiceLocatorInterface', $form->getServiceLocator());
        $this->assertInstanceOf('\Zend\I18n\Translator\Translator', $form->getTranslator());
        $this->assertInstanceOf('\Zend\ServiceManager\ServiceLocatorAwareInterface', $form);
        $this->assertInstanceOf('\Zend\I18n\Translator\TranslatorAwareInterface', $form);
    }
}