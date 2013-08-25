<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

return array(
    'view_helpers' => array(
        'invokables' => array(
            'routeName' => 'Library\View\Helpers\RouteName',
            'showInputError' => 'Library\View\Helpers\RenderInputError',
            'mediaSource' => 'Library\View\Helpers\MediaSource',
            'createFriendlyUrl' => 'Library\View\Helpers\FriendlyUrl',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'DependencyAwareForm' => '\Library\Service\DependencyAwareForm',
            'logger' => 'Zend\Log\LoggerServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        )
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
);
