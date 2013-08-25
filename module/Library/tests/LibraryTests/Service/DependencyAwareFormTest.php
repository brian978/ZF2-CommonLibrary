<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Service;

use Tests\TestHelpers\AbstractTest;

class DependencyAwareFormTest extends AbstractTest
{
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

        /** @var $form \Tests\TestHelpers\Form\AwareForm */
        $form = $service->createForm(array('type' => '\Tests\TestHelpers\Form\AwareForm'));

        $this->assertInstanceOf('\Library\Form\AbstractForm', $form);

        return $form;
    }

    /**
     * @depends testCanCreateFormWithDependencies
     */
    public function testFormImplementsLocatorAwareInterface($form)
    {
        $this->assertInstanceOf('\Zend\ServiceManager\ServiceLocatorAwareInterface', $form);

        return $form;
    }

    /**
     * @depends testFormImplementsLocatorAwareInterface
     */
    public function testFormIsServiceLocatorAware($form)
    {
        $this->assertInstanceOf('\Zend\ServiceManager\ServiceLocatorInterface', $form->getServiceLocator());

        return $form;
    }

    /**
     * @depends testFormIsServiceLocatorAware
     */
    public function testFormImplementTranslatorAwareInterface($form)
    {
        $this->assertInstanceOf('\Zend\I18n\Translator\TranslatorAwareInterface', $form);

        return $form;
    }

    /**
     * @depends testFormImplementTranslatorAwareInterface
     */
    public function testFormIsTranslatorAware($form)
    {
        $this->assertInstanceOf('\Zend\I18n\Translator\Translator', $form->getTranslator());

        return $form;
    }

    /**
     * @depends testFormIsTranslatorAware
     */
    public function testFormImplementsLoggerAwareInterface($form)
    {
        $this->assertInstanceOf('\Library\Log\LoggerAwareInterface', $form);

        return $form;
    }

    /**
     * @depends testFormImplementsLoggerAwareInterface
     */
    public function testFormIsLoggerAware($form)
    {
        $this->assertInstanceOf('\Zend\Log\LoggerInterface', $form->getLogger());
    }
}
