<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Form;

use Library\Components\LoggerAwareObject;
use Library\Components\ServiceLocatorAwareObject;
use Library\Components\TranslatorAwareObject;
use Library\Form\Fieldset\AbstractFieldset;
use Zend\Form\FieldsetInterface;
use Zend\Form\Form;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\InputFilter\InputFilter;
use Library\Log\LoggerAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

abstract class AbstractForm extends Form implements
    TranslatorAwareInterface,
    ServiceLocatorAwareInterface,
    LoggerAwareInterface
{
    use TranslatorAwareObject, LoggerAwareObject, ServiceLocatorAwareObject;

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
     * @param null  $name
     * @param array $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setHydrator(new ClassMethods());
        $this->setInputFilter(new InputFilter());
    }

    /**
     * @param FieldsetInterface $baseFieldset
     * @return $this
     */
    public function setBaseFieldset(FieldsetInterface $baseFieldset)
    {
        parent::setBaseFieldset($baseFieldset);
        $this->setupBaseFieldsetObject($baseFieldset);

        return $this;
    }

    /**
     * @return \Library\Form\Fieldset\AbstractFieldset|null
     */
    protected function getBaseFieldsetObject()
    {
        $object = null;

        if (class_exists($this->baseFieldsetClass)) {
            $object = $this->setupBaseFieldsetObject(new $this->baseFieldsetClass());
        } elseif ($this->baseFieldset instanceof AbstractFieldset) {
            $object = $this->baseFieldset;
        }

        $this->getLogger()->info('The base fieldset has been retrieved and is using the class: ' . get_class($object));

        return $object;
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

        $this->injectFieldsetDependencies($fieldset)->loadElements();

        return $fieldset;
    }

    /**
     * @param AbstractFieldset $fieldset
     * @return $this
     */
    protected function injectFieldsetDependencies(AbstractFieldset $fieldset)
    {
        if ($fieldset instanceof ServiceLocatorAwareInterface) {
            $fieldset->setServiceLocator($this->serviceLocator);
        }

        if ($fieldset instanceof TranslatorAwareInterface) {
            $fieldset->setTranslator($this->getTranslator());
        }

        if ($fieldset instanceof LoggerAwareInterface) {
            $fieldset->setLogger($this->getLogger());
        }

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
}
