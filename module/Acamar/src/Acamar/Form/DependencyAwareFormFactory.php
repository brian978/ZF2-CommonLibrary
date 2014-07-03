<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Form;

use Zend\Form\Factory as ZendFormFactory;
use Zend\Form\FormElementManager;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Acamar\Log\LoggerAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DependencyAwareFormFactory extends ZendFormFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param FormElementManager $formElementManager
     */
    public function __construct(FormElementManager $formElementManager = null)
    {
        parent::__construct($formElementManager);

        $this->serviceLocator = $this->getFormElementManager()->getServiceLocator();
    }

    /**
     * Create an element, fieldset, or form
     *
     * Introspects the 'type' key of the provided $spec, and determines what
     * type is being requested; if none is provided, assumes the spec
     * represents simply an element.
     *
     * @param  array|\Traversable $spec
     *
     * @throws \RuntimeException
     * @return \Zend\Form\ElementInterface
     */
    public function create($spec)
    {
        if ($this->serviceLocator instanceof ServiceLocatorInterface === false) {
            throw new \RuntimeException('Cannot create form because I don\'t have a service locator');
        }

        $form = parent::create($spec);

        if ($form instanceof TranslatorAwareInterface) {
            $form->setTranslator($this->serviceLocator->get('translator'));
        }

        if ($form instanceof ServiceLocatorAwareInterface) {
            $form->setServiceLocator($this->serviceLocator);
        }

        if ($form instanceof LoggerAwareInterface && $this->serviceLocator->has('logger')) {
            /** @var $logger \Zend\Log\LoggerInterface */
            $logger = $this->serviceLocator->get('logger');
            $form->setLogger($logger);
        }

        return $form;
    }
}
