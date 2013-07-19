<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\Service;

use TestHelpers\AbstractTest;

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

        /** @var $form \TestHelpers\Form\AwareForm */
        $form = $service->createForm(array('type' => '\TestHelpers\Form\AwareForm'));

        $this->assertInstanceOf('\Library\Form\AbstractForm', $form);

        return $form;
    }

    /**
     * @depends testCanCreateFormWithDependencies
     */
    public function testFormIsServiceLocatorAware($form)
    {
        $this->assertInstanceOf('\Zend\ServiceManager\ServiceLocatorInterface', $form->getServiceLocator());
//        $this->assertInstanceOf('\Zend\ServiceManager\ServiceLocatorAwareInterface', $form);

        return $form;
    }

    /**
     * @depends testFormIsServiceLocatorAware
     */
    public function testFormIsTranslatorAware($form)
    {
        $this->assertInstanceOf('\Zend\I18n\Translator\Translator', $form->getTranslator());
//        $this->assertInstanceOf('\Zend\I18n\Translator\TranslatorAwareInterface', $form);
    }
}
