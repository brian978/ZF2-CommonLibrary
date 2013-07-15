<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Service;

use Library\Form\DependencyAwareFormFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DependencyAwareForm implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return DependencyAwareFormFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $formElementManager \Zend\Form\FormElementManager */
        $formElementManager = $serviceLocator->get('FormElementManager');

        return new DependencyAwareFormFactory($formElementManager);
    }
}
