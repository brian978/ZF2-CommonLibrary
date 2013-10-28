<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library;

use Library\Form\Factory;
use Zend\Cache\Pattern\ObjectCache;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Cache\PatternFactory;
use Zend\Cache\Storage\Plugin\ClearExpiredByFactor;
use Zend\Cache\Storage\Adapter\Filesystem as FilesystemCache;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface, ServiceProviderInterface
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
                ),
            ),
        );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Cache' => function (ServiceManager $sm) {

                    // Clear expired items plugin
                    $clearExpired = new ClearExpiredByFactor();
                    $clearExpired->getOptions()->setClearingFactor(1);

                    // Serializer plugins
                    $serializer = new Serializer();
                    $serializer->getOptions()->setSerializer('\Zend\Serializer\Adapter\PhpSerialize');

                    // Cache storage setup
                    $cacheStorage = new FilesystemCache(array(
                        'cache_dir' => 'module/Tests/caches',
                        'ttl' => 10,
                    ));

                    $cacheStorage->addPlugin($clearExpired);
                    $cacheStorage->addPlugin($serializer);

                    /** @var $cache ObjectCache */
                    $cache = PatternFactory::factory(
                        'object',
                        array(
                            'object' => new \stdClass(),
                            'storage' => $cacheStorage,
                            'cache_output' => false,
                            'cache_by_default' => true,
                        )
                    );

                    return $cache;
                },
            )
        );
    }
}
