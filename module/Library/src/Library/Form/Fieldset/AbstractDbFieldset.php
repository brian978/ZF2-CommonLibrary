<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Form\Fieldset;

use Library\Model\AbstractDbModel;
use Library\Model\DbModelAwareInterface;

abstract class AbstractDbFieldset extends AbstractFieldset implements DbModelAwareInterface
{
    /**
     * This mode is usually used when the fieldset element is used for a drop-down list
     */
    const MODE_SELECT = 2;

    /**
     * @var \Library\Model\AbstractDbModel
     */
    protected $model;

    /**
     * Contains an array of model names that can be retrieved using the service manager
     *
     * @var array
     */
    protected $fieldsetModels = array();

    /**
     * @var string
     */
    protected $modelName = '';

    /**
     * This is used when the setModel() method is called twice (like when extending a form)
     *
     * @var boolean
     */
    protected $lockModel = false;

    /**
     * Initialized the model required for the database
     *
     * @param $serviceName
     * @return $this
     */
    protected function initModel($serviceName)
    {
        if (!is_object($this->model) || $this->lockModel === false) {
            $this->model = $this->serviceLocator->get($serviceName);
        }

        return $this;
    }

    /**
     * @param $lockModel
     * @return $this
     */
    public function setLockModel($lockModel)
    {
        $this->lockModel = $lockModel;

        return $this;
    }

    /**
     * @return bool
     */
    public function isModelLocked()
    {
        return $this->lockModel;
    }

    /**
     * @param $fieldsetName
     * @param $serviceName
     * @return $this
     */
    public function addServiceModel($fieldsetName, $serviceName)
    {
        $this->fieldsetModels[$fieldsetName] = $serviceName;

        return $this;
    }

    /**
     * @param $fieldsetModels
     * @return $this
     */
    public function setServiceModels($fieldsetModels)
    {
        $this->fieldsetModels = $fieldsetModels;

        return $this;
    }

    /**
     * This can be used when adding new fieldsets to the current one
     *
     * @param AbstractFieldset $fieldset
     * @return AbstractFieldset
     */
    public function addFieldset(AbstractFieldset $fieldset)
    {
        $fieldset      = parent::addFieldset($fieldset);
        $fieldsetClass = get_class($fieldset);

        // Injecting the proper model into the fieldset
        if ($fieldset instanceof AbstractDbFieldset && isset($this->fieldsetModels[$fieldsetClass])) {
            $fieldset->setModel($this->getServiceLocator()->get($this->fieldsetModels[$fieldsetClass]));
            $fieldset->setServiceModels($this->fieldsetModels[$fieldsetClass]);
        }

        return $fieldset;
    }

    /**
     * @param $label
     * @return array
     */
    protected function getSelectId($label)
    {
        return array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id',
            'options' => array(
                'label' => $label,
                'label_attributes' => array(
                    'class' => 'form_row'
                ),
                'value_options' => $this->getValueOptions()
            ),
            'attributes' => array(
                'required' => true
            )
        );
    }

    /**
     * @return array
     */
    protected function getHiddenId()
    {
        return array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'options' => array(
                'value' => 0
            )
        );
    }

    /**
     *
     * @param string $label
     *
     * @return array
     */
    protected function getIdElement($label)
    {
        if ($this->mode === self::MODE_SELECT) {
            $element = $this->getSelectId($label);
        } else {
            $element = $this->getHiddenId();
        }

        return $element;
    }

    /**
     * Builds an array of options for the select box
     *
     * @return array
     */
    protected function getValueOptions()
    {
        if (!is_object($this->model)) {
            $this->initModel($this->modelName);
        }

        $options = array(
            0 => '...'
        );

        foreach ($this->model->fetch() as $value => $row) {
            $options[$value] = $row['name'];
        }

        return $options;
    }

    /**
     * @param AbstractDbModel $model
     * @return $this
     */
    public function setModel(AbstractDbModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return AbstractDbModel
     */
    public function getModel()
    {
        return $this->model;
    }
}
