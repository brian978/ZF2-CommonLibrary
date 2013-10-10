<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

return array(
    'router' => array(
        'routes' => array(
            'auth' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/auth[/:action[/:status]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'status' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array()
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Index' => 'Auth\Controller\IndexController'
        )
    ),
    'service_manager' => array(
        'invokables' => array(
            'redirectUrl' => '\Auth\Services\Helper\RedirectUrl',
        ),
        'factories' => array(
            'authorization' => 'Auth\Services\AuthorizationFactory',
            'logger' => 'Zend\Log\LoggerServiceFactory',
            'AuthenticationService' => '\Auth\Services\AuthenticationServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
            'Zend\Authentication\AuthenticationService' => 'AuthenticationService',
        ),
    ),
    'log' => array(
        'writers' => array(
            array(
                'name' => 'stream',
                'priority' => 5,
                'options' => array(
                    'stream' => 'php://stderr',
                )
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/auth.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
);
