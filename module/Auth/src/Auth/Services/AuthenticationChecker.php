<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-AuthModule
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Services;

use Auth\Model\Authentication;
use Auth\Model\Helper\Password;
use \Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Exception\InvalidServiceNameException;

/**
 * Class AuthenticationVerifier (pending)
 *
 * @package Auth\Services
 */
class AuthenticationChecker
{
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @param MvcEvent $event
     */
    public function __construct(MvcEvent $event)
    {
        $this->event = $event;
    }

    /**
     * The method is used to check if the user is authenticated
     */
    public function checkAuthStatus()
    {
        /** @var $response HttpResponse */
        $response = $this->event->getResponse();

        if ($response instanceof HttpResponse) {

            /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
            $serviceManager = $this->event->getApplication()->getServiceManager();

            // Checking for the AuthAdapter service
            if (!$serviceManager->has('AuthAdapter')) {
                $serviceManager->setFactory('AuthAdapter', '\Zend\Db\Adapter\AdapterServiceFactory');
            }

            /** @var $logger \Zend\Log\Logger */
            $logger = $serviceManager->get('logger');
            $url    = null;

            try {
                /** @var $dbAdapter \Zend\Db\Adapter\Adapter */
                $dbAdapter = $serviceManager->get('AuthAdapter');
            } catch (ServiceNotCreatedException $e) {
                $logger->alert($e->getMessage(), array('file' => __FILE__));
            }

            if (isset($dbAdapter)) {

                /** @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
                $router = $this->event->getRouter();

                // We check for the service first to avoid having multiple instances of the
                // Authentication object at application level
                if (!$serviceManager->has('Auth\Authentication')) {
                    /** @var $auth \Auth\Model\Authentication */
                    $auth = $serviceManager->get('AuthenticationService');
                    $auth->setDbAdapter($dbAdapter);
                } else {
                    /** @var $auth \Auth\Model\Authentication */
                    $auth = $serviceManager->get('Auth\Authentication');
                }

                /** @var $redirectUrlHelper \Auth\Services\Helper\RedirectUrl */
                $redirectUrlHelper = $serviceManager->get('redirectUrl');

                $redirectUrlHelper->setAuth($auth)
                    ->setEvent($this->event)
                    ->setRouter($router);

                $url = $redirectUrlHelper->get();
            }

            if (!empty($url)) {
                $response->getHeaders()->addHeaderLine('Location: ' . $url);
                $response->setStatusCode(HttpResponse::STATUS_CODE_302);
            }
        }
    }
}
