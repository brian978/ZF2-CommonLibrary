<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-AuthModule
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AuthTests\Model;

use Auth\Model\Authorization;
use Auth\Model\AuthorizationDbInterface;
use Auth\Model\Helper\Password;
use PHPUnit_Framework_TestCase;
use Auth\Model\Authentication;
use Tests\TestHelpers\Traits\AuthenticationMocks;
use Zend\Authentication\Result;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class AuthTest extends PHPUnit_Framework_TestCase
{
    use AuthenticationMocks;

    /**
     * @return Result
     */
    public function getAuthResult()
    {
        $code = 0;

        if (!empty($this->givenPassword)) {
            if ($this->givenPassword == $this->password) {
                $code = 1;
            }
        }

        return new Result($code, $this->email);
    }

    public function setUp()
    {
        // Retrieving the adapters for the mock
        $adapters = $this->getAdapterMocks();

        $this->authObject = new Authentication($adapters['dbAdapter'], new Password());
        $this->authObject->setAdapter($adapters['authAdapter']);
    }

    public function tearDown()
    {
        $this->givenPassword = '';
    }

    public function testAuthenticateSuccess()
    {
        $this->givenPassword = 'test';

        $this->authObject->setCredentials(
            array(
                'email' => $this->email,
                'password' => $this->givenPassword,
            )
        );

        try {
            $result = $this->authObject->authenticate();
        } catch (\Exception $e) {
            $result = null;
        }

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);
        $this->assertTrue($result->isValid());
        $this->assertEquals($this->email, $this->authObject->getIdentity());
    }

    public function testAuthenticateFail()
    {
        $this->givenPassword = 'test2';

        $this->authObject->setCredentials(
            array(
                'email' => $this->email,
                'password' => $this->givenPassword,
            )
        );

        try {
            $result = $this->authObject->authenticate();
        } catch (\Exception $e) {
            $result = null;
        }

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);
        $this->assertFalse($result->isValid());
    }

    public function authProvider()
    {
        // We need the data from the setUp() method
        $this->setUp();

        $authorizationModel = $this->getMockBuilder('\Auth\Model\AuthorizationDbInterface')
            ->getMock();

        /** @var $authorizationModel AuthorizationDbInterface */
        $authorization = new Authorization($authorizationModel, $this->authObject);

        return array(
            array($authorizationModel, $authorization)
        );
    }

    /**
     * @dataProvider authProvider
     * @param $authorizationModel \PHPUnit_Framework_MockObject_MockObject
     * @param $authorization      Authorization
     */
    public function testGetAuthorizationRole($authorizationModel, $authorization)
    {
        $authorizationModel->expects($this->any())
            ->method('getInfoByIdentity')
            ->will($this->returnValue(new \ArrayObject(array('roleName' => 'admin'))));

        // Setting the credentials
        $this->givenPassword = 'test';

        $this->authObject->setCredentials(
            array(
                'email' => $this->email,
                'password' => $this->givenPassword,
            )
        );

        $this->assertEquals('admin', $authorization->getRole());
    }

    /**
     * @dataProvider          authProvider
     * @param $authorizationModel \PHPUnit_Framework_MockObject_MockObject
     * @param $authorization      Authorization
     * @expectedException \RuntimeException
     * @expectedExceptionCode 10
     */
    public function testAuthorizationModelProvidesIncorrectValue($authorizationModel, $authorization)
    {
        $authorizationModel->expects($this->any())
            ->method('getInfoByIdentity')
            ->will($this->returnValue(false));

        $authorization->getRole();
    }

    /**
     * @dataProvider          authProvider
     * @param $authorizationModel \PHPUnit_Framework_MockObject_MockObject
     * @param $authorization      Authorization
     * @expectedException \RuntimeException
     * @expectedExceptionCode 20
     */
    public function testAuthorizationModelProvidesNoData($authorizationModel, $authorization)
    {
        $authorizationModel->expects($this->any())
            ->method('getInfoByIdentity')
            ->will($this->returnValue(new \ArrayObject()));

        $authorization->getRole();
    }

    /**
     * @dataProvider          authProvider
     * @param $authorizationModel \PHPUnit_Framework_MockObject_MockObject
     * @param $authorization      Authorization
     * @expectedException \RuntimeException
     * @expectedExceptionCode 30
     */
    public function testAuthorizationModelDoesNotProvideRequiredData($authorizationModel, $authorization)
    {
        $authorizationModel->expects($this->any())
            ->method('getInfoByIdentity')
            ->will($this->returnValue(new \ArrayObject(array('foo' => 'admin'))));

        $authorization->getRole();
    }
}
