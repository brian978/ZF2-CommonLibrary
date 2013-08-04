<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Form;

use Library\Form\Fieldset\AbstractFieldset;
use Library\Log\DummyLogger;
use Zend\Form\Form;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\InputFilter\InputFilter;
use Library\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

abstract class AbstractForm extends Form implements
    TranslatorAwareInterface,
    ServiceLocatorAwareInterface,
    LoggerAwareInterface
{
    const MODE_ADD  = 1;
    const MODE_EDIT = 2;

    /**
     * The class name of the base fieldset object
     *
     * @var string
     */
    protected $baseFieldsetClass = '';

    /**
     * Determines the mode of the form to be able to activate or deactivate certain fields
     *
     * @var int
     */
    public $mode = self::MODE_ADD;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param  null|int|string $name    Optional name for the element
     * @param  array           $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setHydrator(new ClassMethods(false));
        $this->setInputFilter(new InputFilter());
    }

    /**
     * @param AbstractFieldset $fieldset
     * @return $this
     */
    public function setBaseFieldset(AbstractFieldset $fieldset)
    {
        parent::setBaseFieldset($fieldset);

        return $this;
    }

    /**
     * @return AbstractFieldset|null
     */
    protected function getBaseFieldsetObject()
    {
        if (class_exists($this->baseFieldsetClass)) {
            $this->getLogger()->info('Base fieldset created using class name');
            return $this->setupBaseFieldsetObject(new $this->baseFieldsetClass());
        } elseif($this->baseFieldset instanceof AbstractFieldset) {
            $this->getLogger()->info('Base fieldset created using class object');
            return $this->setupBaseFieldsetObject($this->baseFieldset);
        }

        $this->getLogger()->crit('The base fieldset could not be created');

        return null;
    }

    /**
     * This function is used to set up the base fieldset object in a primitive way
     * For something more complex use only the getBaseFieldsetObject() method
     *
     * @param Fieldset\AbstractFieldset $fieldset
     * @return AbstractFieldset
     */
    final protected function setupBaseFieldsetObject(AbstractFieldset $fieldset)
    {
        $fieldset->setUseAsBaseFieldset(true);

        if ($this->mode === self::MODE_EDIT) {
            $fieldset->addFilter('id');
        }

        if ($fieldset instanceof ServiceLocatorAwareInterface) {
            $fieldset->setServiceLocator($this->serviceLocator);
        }

        if ($fieldset instanceof TranslatorAwareInterface) {
            $fieldset->setTranslator($this->getTranslator());
        }

        if ($fieldset instanceof LoggerAwareInterface) {
            $fieldset->setLogger($this->getLogger());
        }

        $fieldset->loadElements();

        return $fieldset;
    }

    /**
     * Initializes the elements of the form
     *
     * @return $this
     */
    public function loadElements()
    {
        // Adding the elements
        $this->add($this->getBaseFieldsetObject())
            ->add(
                array(
                    'type' => 'Zend\Form\Element\Csrf',
                    'name' => 'csrf'
                )
            )->add(
                array(
                    'name' => 'submit',
                    'attributes' => array(
                        'type' => 'submit',
                        'value' => 'Send'
                    )
                )
            );

        return $this;
    }

    /**
     * Sets translator to use in helper
     *
     * @param  Translator $translator  [optional] translator.
     *                                 Default is null, which sets no translator.
     * @param  string     $textDomain  [optional] text domain
     *                                 Default is null, which skips setTranslatorTextDomain
     *
     * @return TranslatorAwareInterface
     */
    public function setTranslator(Translator $translator = null, $textDomain = null)
    {
        $this->translator = $translator;

        // noting to set
        unset($textDomain);

        return $this;
    }

    /**
     * Returns translator used in object
     *
     * @return Translator|null
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Checks if the object has a translator
     *
     * @return bool
     */
    public function hasTranslator()
    {
        return is_object($this->translator);
    }

    /**
     * Sets whether translator is enabled and should be used
     *
     * @param  bool $enabled [optional] whether translator should be used.
     *                       Default is true.
     *
     * @return TranslatorAwareInterface
     */
    public function setTranslatorEnabled($enabled = true)
    {
        // nothing to set
        unset($enabled);

        return $this->translator;
    }

    /**
     * Returns whether translator is enabled and should be used
     *
     * @return bool
     */
    public function isTranslatorEnabled()
    {
        return true;
    }

    /**
     * Set translation text domain
     *
     * @param  string $textDomain
     *
     * @return TranslatorAwareInterface
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        // nothing to set
        unset($textDomain);

        return $this->translator;
    }

    /**
     * Return the translation text domain
     *
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        return '';
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return \Zend\Log\LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->logger instanceof LoggerInterface) {
            $this->logger = new DummyLogger();
        }

        return $this->logger;
    }
}
