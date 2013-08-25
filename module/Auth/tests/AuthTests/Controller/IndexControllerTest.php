<?php
/**
 * ZF2-AuthModule
 *
 */

namespace AuthTests\Controller;

use Tests\TestHelpers\ApplicationConfig;
use Tests\TestHelpers\Db\Adapter\Platform;
use Tests\TestHelpers\Traits\AuthenticationMocks;
use Zend\Db\Sql\Select;
use Zend\I18n\Translator\Translator;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Http\Request as HttpRequest;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    use AuthenticationMocks;

    public function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(ApplicationConfig::getConfig());

        // Creating the DbAdapter Mock
        $adapter = $this->getMockBuilder('\Zend\Db\Adapter\Adapter')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue(new Platform()));

        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $this->getApplicationServiceLocator();

        $serviceManager->setService('AuthAdapter', $adapter);

        // Reset given password
        $this->givenPassword = '';
    }

    public function testServiceManagerHasAuthAdapter()
    {
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $this->getApplicationServiceLocator();

        $this->assertTrue($serviceManager->has('AuthAdapter'));
    }

    public function testHasAuthenticationService()
    {
        $this->dispatch('/auth');
        $this->assertTrue($this->getApplicationServiceLocator()->has('Auth\Authentication'));
    }

    public function testNotLoggedIn()
    {
        $this->dispatch('/auth');

        /** @var $auth \Auth\Model\Authentication */
        $auth = $this->getApplicationServiceLocator()->get('Auth\Authentication');

        $this->assertEmpty($auth->getIdentity());
    }

    public function testIndexCanBeAccessed()
    {
        $this->dispatch('/auth/index');
        $this->assertMatchedRouteName('auth');
        $this->assertActionName('index');
        $this->assertResponseStatusCode(200);
    }

    public function testRedirectWhenLogOut()
    {
        // Replacing the authentication service with a dummy one
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $this->getApplicationServiceLocator();

        $auth = $this->getMockBuilder('\Auth\Model\Authentication')
            ->disableOriginalConstructor()
            ->getMock();

        $auth->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $auth->expects($this->any())
            ->method('clearIdentity')
            ->will($this->returnValue(true));

        // Adding the service from here so it's not set in the module class
        $serviceManager->setService('Auth\Authentication', $auth);

        // We need to override some config options or else this will throw an error
        $serviceManager->setAllowOverride(true);

        $config                                     = $serviceManager->get('Config');
        $config['auth_module']['redirect_to_login'] = false;

        $serviceManager->setService('Config', $config);
        $serviceManager->setAllowOverride(false);

        $this->dispatch('/auth/logout');
        $this->assertMatchedRouteName('auth');
        $this->assertActionName('logout');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/auth/index/logged_out');
    }

    public function testRedirectWhenNotAuthenticated()
    {
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $this->getApplicationServiceLocator();

        // We need to override some config options or else this will throw an error
        $serviceManager->setAllowOverride(true);

        $config                                     = $serviceManager->get('Config');
        $config['auth_module']['redirect_to_login'] = true;

        $serviceManager->setService('Config', $config);
        $serviceManager->setAllowOverride(false);

        $this->dispatch('/auth/logout');
        $this->assertMatchedRouteName('auth');
        $this->assertActionName('logout');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/auth');
    }

    public function testLoginCanBeAccessed()
    {
        $this->dispatch('/auth/login');
        $this->assertMatchedRouteName('auth');
        $this->assertActionName('login');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/auth/index/fail');
    }

    public function testIdentityViewHelperCanBeCreated()
    {
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $this->getApplicationServiceLocator();

        /** @var $renderer \Zend\View\Renderer\PhpRenderer */
        $renderer = $serviceManager->get('ViewManager')->getRenderer();

        $this->assertInstanceOf('\Zend\View\Helper\Identity', $renderer->getHelperPluginManager()->get('identity'));
    }

    public function testCanSetSessionExpiration()
    {
        // Retrieving the adapters for the mock
        $adapters = $this->getAdapterMocks();

        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $this->getApplicationServiceLocator();

        /** @var $auth \Auth\Model\Authentication */
        $auth = $serviceManager->get('AuthenticationService');
        $auth->setDbAdapter($adapters['dbAdapter']);
        $auth->setAdapter($adapters['authAdapter']);

        // Changing the given password so the login is successful
        $this->givenPassword = 'test';

        // We need to override the login success route in order for the test to work properly
        $serviceManager->setAllowOverride(true);

        $config                                     = $serviceManager->get('Config');
        $config['auth_module']['loginSuccessRoute'] = 'auth';

        $serviceManager->setService('Config', $config);
        $serviceManager->setAllowOverride(false);

        $this->dispatch('/auth/login', HttpRequest::METHOD_POST);
        $this->assertMatchedRouteName('auth');
        $this->assertActionName('login');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/auth');
    }
}
