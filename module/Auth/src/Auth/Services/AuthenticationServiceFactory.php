<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Services;

use Auth\Authentication\Storage;
use Auth\Model\Authentication;
use Auth\Model\Helper\Password;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->get('ServiceManager');

        /** @var $logger \Zend\Log\Logger */
        $logger = $serviceLocator->get('logger');

        $auth = new Authentication(null, new Password());
        $auth->setStorage(new Storage\Session());
        $auth->setLogger($logger);

        try {
            $serviceManager->setService('Auth\Authentication', $auth);
        } catch (\Exception $e) {
            $logger->notice($e->getMessage(), array('file' => __FILE__));
        }

        return $auth;
    }
}
