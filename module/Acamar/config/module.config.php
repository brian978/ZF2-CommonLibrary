<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

return array(
    'view_helpers' => array(
        'invokables' => array(
            'routeName' => 'Acamar\View\Helpers\RouteName',
            'showInputError' => 'Acamar\View\Helpers\RenderInputError',
            'mediaSource' => 'Acamar\View\Helpers\MediaSource',
            'createFriendlyUrl' => 'Acamar\View\Helpers\FriendlyUrl',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'DependencyAwareForm' => '\Acamar\Service\DependencyAwareForm',
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
