<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-AuthModule
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractAuthController extends AbstractActionController
{
    /**
     * @var array
     */
    protected $config = array();

    /**
     * @return array
     */
    abstract protected function getConfig();

    /**
     * @return string
     */
    abstract protected function createLogInUrl();

    /**
     * @return string
     */
    abstract protected function createLoginSuccessUrl();

    /**
     * @return string
     */
    abstract protected function createLoginFailedUrl();

    /**
     * @return string
     */
    abstract protected function createLoggedOutUrl();
}
