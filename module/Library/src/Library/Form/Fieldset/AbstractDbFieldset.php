<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
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
    public function getLockModel()
    {
        return $this->lockModel;
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
