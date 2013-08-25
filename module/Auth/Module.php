<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-AuthModule
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth;

use Auth\Services\AuthenticationChecker;
use Zend\Http\Response as HttpResponse;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    /**
     * @var string
     */
    protected $moduleDir = __DIR__;

    /**
     * @var string
     */
    protected $moduleNamespace = __NAMESPACE__;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include $this->moduleDir . '/config/module.config.php';
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->moduleNamespace => $this->moduleDir . '/src/' . $this->moduleNamespace,
                )
            )
        );
    }

    /**
     * @param MvcEvent $event
     * @return HttpResponse
     */
    public function onBootstrap(MvcEvent $event)
    {
        $this->event = $event;

        /** @var $eventManager \Zend\EventManager\EventManager */
        $eventManager = $this->event->getApplication()->getEventManager();

        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'loadAuthChecker'));
    }

    /**
     * This is used so we can easily inject a custom method of checking the authentication status
     *
     * The config could have been used but then it would create a loading priority dependency between
     * the Authentication module and the module where the custom factory/service would have been registered
     *
     * @param MvcEvent $e
     */
    public function loadAuthChecker(MvcEvent $e)
    {
        /** @var $eventManager \Zend\EventManager\EventManager */
        $eventManager = $e->getApplication()->getEventManager();
        $listeners    = $eventManager->getListeners('checkAuthStatus');

        if ($listeners->count() == 1) {
            $eventManager->trigger('checkAuthStatus', $e);
        } else {
            $authChecker = new AuthenticationChecker($e);
            $authChecker->checkAuthStatus();
        }
    }
}
