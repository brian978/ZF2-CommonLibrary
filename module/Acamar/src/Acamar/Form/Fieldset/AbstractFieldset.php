<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Form\Fieldset;

use Acamar\Components\LoggerAwareObject;
use Acamar\Components\ServiceLocatorAwareObject;
use Acamar\Components\TranslatorAwareObject;
use Zend\Form\Fieldset;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Acamar\Log\LoggerAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

abstract class AbstractFieldset extends Fieldset implements
    InputFilterProviderInterface,
    ServiceLocatorAwareInterface,
    TranslatorAwareInterface,
    LoggerAwareInterface
{
    use TranslatorAwareObject, LoggerAwareObject, ServiceLocatorAwareObject;

    const MODE_ADMIN = 1;

    /**
     * Depending on this mode the object will add the ID element differently
     *
     * @var int
     */
    public $mode = self::MODE_ADMIN;

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * Used to add all elements to the fieldset
     *
     * @return mixed
     */
    abstract public function loadElements();

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setHydrator(new ClassMethods());
    }

    /**
     * This can be used when adding new fieldsets to the current one
     *
     * @param AbstractFieldset $fieldset
     * @return AbstractFieldset
     */
    protected function addFieldset(AbstractFieldset $fieldset)
    {
        $this->add($fieldset);

        $fieldset->setServiceLocator($this->getServiceLocator());
        $fieldset->setTranslator($this->getTranslator());
        $fieldset->setLogger($this->getLogger());

        return $fieldset;
    }

    /**
     * Sets a list of filters by replacing the current list
     *
     * @param array $filters
     * @return $this
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Adds a filter or an array of filters to the filter list
     *
     * @param $filter
     * @return $this
     */
    public function addFilter($filter)
    {
        if (is_array($filter)) {
            $this->filters = array_merge($this->filters, $filter);
        } elseif (!isset($this->filters[$filter])) {
            $this->filters [$filter] = true;
        }

        return $this;
    }

    /**
     * These are a set of input filters that are used by most forms
     *
     * @return array
     */
    protected function getGenericInputFilterSpecs()
    {
        $availableFilters = array(
            'id' => array(
                'validators' => array(
                    array(
                        'name' => 'greater_than',
                        'options' => array(
                            'min' => 0,
                            'message' => $this->translator->translate('You must select a value')
                        )
                    )
                )
            ),
            'name' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 2,
                            'message' => sprintf($this->translator->translate('Minimum length is %u'), 2)
                        )
                    )
                )
            )
        );

        return $availableFilters;
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array_intersect_key($this->getGenericInputFilterSpecs(), $this->filters);
    }
}
