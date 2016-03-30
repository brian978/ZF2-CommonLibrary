<?php

/**
 * Layout loader module for Zend Framework 2
 *
 * @author Brian
 * @link https://github.com/brian978/ZF2-LayoutLoader
 * @copyright 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 * @version 1.4
 */

namespace LayoutLoader;

use Zend\EventManager\EventInterface;
use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package LayoutLoader
 */
class Module implements BootstrapListenerInterface
{
    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface|MvcEvent $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        /** @var $app \Zend\Mvc\Application */
        $app = $e->getApplication();

        // Registering trigger for the dispatch event
        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) {
            $controllerNamespace = current(explode('\\', $e->getRouteMatch()->getParam('controller')));
            $model               = $e->getViewModel();

            // The view model must a standalone model
            if ($model->terminate() !== true) {
                $filterChain = new FilterChain();
                $filterChain->attach(new CamelCaseToDash());
                $filterChain->attach(new StringToLower());

                $model->setTemplate($filterChain->filter($controllerNamespace) . '/layout');
            }
        });
    }
}
