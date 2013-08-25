<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-AuthModule
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Controller;

use Auth\Model\Authentication;
use Zend\Mvc\MvcEvent;

class IndexController extends AbstractAuthController
{
    /**
     * These are the default here in case nothing is set in the autoloaded config
     * If you want to change something change them in the autoloaded config not in this one
     *
     * @var array
     */
    protected $config = array();

    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    /**
     * @return array
     */
    protected function getConfig()
    {
        if (empty($this->config)) {
            $this->config = array(
                'loginRoute' => 'auth',
                'loginFailedRoute' => 'auth',
                'loginSuccessRoute' => 'index',
                'loggedOutRoute' => 'auth',
                'rememberFlag' => 'remember_me',
                'rememberExpire' => 2592000,
            );

            // Loading the module config and merging
            $config = $this->getEvent()->getApplication()->getConfig();
            if (isset($config['auth_module'])) {
                $this->config = array_merge($this->config, $config['auth_module']);
            }
        }

        return $this->config;
    }

    /**
     * @return string
     */
    protected function createLogInUrl()
    {
        return $this->url()->fromRoute($this->config['loginRoute'], array('action' => 'login'));
    }

    /**
     * @return string
     */
    protected function createLoginSuccessUrl()
    {
        return $this->url()->fromRoute($this->config['loginSuccessRoute'], array('action' => 'index'));
    }

    /**
     * @return string
     */
    protected function createLoginFailedUrl()
    {
        return $this->url()->fromRoute(
            $this->config['loginFailedRoute'],
            array(
                'action' => 'index',
                'status' => 'fail'
            )
        );
    }

    /**
     * @return string
     */
    protected function createLoggedOutUrl()
    {
        return $this->url()->fromRoute(
            $this->config['loggedOutRoute'],
            array('action' => 'index', 'status' => 'logged_out')
        );
    }

    /**
     * Sets the proper layout
     *
     * @param MvcEvent $event
     * @return mixed
     */
    public function onDispatch(MvcEvent $event)
    {
        // Loading the config
        $this->getConfig();

        // Loading the logger
        $this->logger = $this->serviceLocator->get('logger');

        return parent::onDispatch($event);
    }

    public function indexAction()
    {
        return array(
            'loginUrl' => $this->createLogInUrl(),
        );
    }

    public function loginAction()
    {
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
        $success = false;

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();

            /** @var $auth Authentication */
            try {
                $auth = $this->serviceLocator->get('Auth\Authentication');
                $auth->setCredentials($post);
                $result = $auth->authenticate();
            } catch (\Exception $e) {
                $this->logger->alert($e->getMessage(), array('file' => __FILE__));
            }

            // Setting success style and session expiration
            if (isset($result) && is_object($result) && $result->isValid()) {
                if (isset($post[$this->config['rememberFlag']])) {
                    /** @var $session \Zend\Session\AbstractContainer */
                    $session = $auth->getStorage()->getSessionContainer();
                    $session->setExpirationSeconds($this->config['rememberExpire']);

                    // Logging info about the expiration time
                    $this->logger->debug('Session will expire in ' . $this->config['rememberExpire'] . ' seconds');
                }

                $success = true;
            }
        }

        if ($success === true) {
            $this->redirect()->toUrl($this->createLoginSuccessUrl());
        } else {
            $this->redirect()->toUrl($this->createLoginFailedUrl());
        }
    }

    public function logoutAction()
    {
        try {
            /** @var $auth \Auth\Model\Authentication */
            $auth = $this->serviceLocator->get('Auth\Authentication');
            $auth->clearIdentity();
        } catch (\Exception $e) {
            $this->logger->crit($e->getMessage(), array('file' => __FILE__));
        }

        $this->redirect()->toUrl($this->createLoggedOutUrl());
    }
}
