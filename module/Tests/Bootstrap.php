<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests;

use Tests\TestHelpers\ApplicationConfig;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

class Bootstrap
{
    public static function init()
    {
        /**
         * ------------------------
         * LOADING AUTOLOADER
         * ------------------------
         */
        static::initAutoloader();

        /**
         * ------------------------
         * LOADING CONFIG
         * ------------------------
         */
        ApplicationConfig::setConfig(require __DIR__ . '/config/application.config.php');

        /**
         * ------------------------
         * LOADING MODULES
         * ------------------------
         */
        self::loadModules();
    }

    /**
     * The method loads the modules that are required for testing models
     * or other components (the controllers load the application so this is not required for the controllers)
     *
     */
    protected static function loadModules()
    {
        // Loading the modules
        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', ApplicationConfig::getConfig());

        /** @var $moduleManager \Zend\ModuleManager\ModuleManager */
        $moduleManager = $serviceManager->get('ModuleManager');
        $moduleManager->loadModules();
    }

    protected static function initAutoloader()
    {
        $vendorPath = './vendor';

        $zf2Path = false;

        if ($vendorPath !== false && is_dir($vendorPath . '/ZF2/library')) {
            $zf2Path = $vendorPath . '/ZF2/library';
        } elseif (getenv('ZF2_PATH')) { // Support for ZF2_PATH environment variable or git sub-module
            $zf2Path = getenv('ZF2_PATH');
        } elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
            $zf2Path = get_cfg_var('zf2_path');
        }

        if (!$zf2Path) {
            throw new \RuntimeException('Could not find the ZF2 library.');
        }

        if (file_exists($vendorPath . '/autoload.php')) {
            include $vendorPath . '/autoload.php';
        }

        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        AutoloaderFactory::factory(
            array(
                'Zend\Loader\StandardAutoloader' => array(
                    'autoregister_zf' => true,
                    'namespaces' => array(
                        'Tests' => __DIR__,
                        'Mocks' => __DIR__ . '/Mocks',
                    )
                )
            )
        );
    }
}
