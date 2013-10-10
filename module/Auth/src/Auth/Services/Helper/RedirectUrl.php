<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Services\Helper;

use Auth\Model\Authentication;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack;

class RedirectUrl
{
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @var TreeRouteStack
     */
    protected $router;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @param \Auth\Model\Authentication $auth
     * @return $this
     */
    public function setAuth(Authentication $auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Sets the event object as well as the configuration from the module
     *
     * @param MvcEvent $event
     * @return $this
     */
    public function setEvent(MvcEvent $event)
    {
        $this->event = $event;

        // Don't like this, find better way
        $this->getConfig();

        return $this;
    }

    /**
     * @param TreeRouteStack $router
     * @return $this
     */
    public function setRouter(TreeRouteStack $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * @return $this
     */
    public function getConfig()
    {
        $config       = $this->event->getApplication()->getConfig();
        $this->config = $config['auth_module'];

        return $this;
    }

    /**
     * @param string $routeName
     * @param string $actionName
     * @throws \RuntimeException
     * @return bool
     */
    protected function checkIfMustRedirectToLogin($routeName, $actionName)
    {
        if (!is_string($routeName) || !is_string($actionName)) {
            throw new \RuntimeException('The route and action name must be strings');
        }

        $result = false;

        if ($routeName != $this->config['loginRoute']
            || ($routeName == $this->config['loginRoute'] && $actionName != 'index' && $actionName != 'login')
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Returns the URL where to redirect
     *
     * @return string
     */
    public function get()
    {
        if ($this->config['redirect_to_login'] === false) {
            return '';
        }

        $url        = '';
        $routeMatch = $this->router->match($this->event->getRequest());
        $routeName  = $routeMatch->getMatchedRouteName();
        $actionName = $routeMatch->getParam('action');

        if (false === $this->auth->hasIdentity()) {

            // Executed when not logged in and not on the auth route
            if ($this->checkIfMustRedirectToLogin($routeName, $actionName)) {
                $url = $this->router->assemble(
                    array(),
                    array(
                        'name' => $this->config['loginRoute'],
                    )
                );
            }
        } else {

            // Executed when already logged in
            if ($routeName == $this->config['loginRoute']) {
                $url = $this->router->assemble(
                    array(),
                    array(
                        'name' => $this->config['alreadyLoggedInRoute'],
                    )
                );
            }
        }

        return $url;
    }
}
