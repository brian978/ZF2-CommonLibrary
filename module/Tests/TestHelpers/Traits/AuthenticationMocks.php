<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Traits;

use Auth\Model\Helper\Password;
use Tests\TestHelpers\Db\Adapter\Platform;
use Zend\Authentication\Result;
use Zend\Db\Sql\Select;

trait AuthenticationMocks
{
    /**
     * @var \Auth\Model\Authentication
     */
    protected $authObject;

    /**
     * @var string
     */
    protected $email = 'test@test.com';

    /**
     * @var string
     */
    protected $password = 'test';

    /**
     * @var string
     */
    protected $givenPassword = '';

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

    /**
     * @return array
     */
    protected function getAdapterMocks()
    {
        $sqlSelect      = new Select();
        $passwordHelper = new Password($this->password);

        // Mocking the result set
        $resultSet = $this->getMockBuilder('\Zend\Db\ResultSet\ResultSet')
            ->getMock();

        $resultSet->expects($this->any())
            ->method('current')
            ->will(
                $this->returnValue(
                    new \ArrayObject(
                        array(
                            'email' => $this->email,
                            'password' => $passwordHelper->generateHash()
                        )
                    )
                )
            );

        // Mocking the database adapter
        $dbAdapter = $this->getMockBuilder('\Zend\Db\Adapter\Adapter')
            ->disableOriginalConstructor()
            ->getMock();

        $dbAdapter->expects($this->any())
            ->method('query')
            ->will($this->returnValue($resultSet));

        $dbAdapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue(new Platform()));

        // Mocking the authentication adapter
        $authAdapter = $this->getMockBuilder('\Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        $authAdapter->expects($this->any())
            ->method('authenticate')
            ->will($this->returnCallback(array($this, 'getAuthResult')));

        $authAdapter->expects($this->any())
            ->method('getDbSelect')
            ->will($this->returnValue($sqlSelect));

        return array(
            'authAdapter' => $authAdapter,
            'dbAdapter' => $dbAdapter,
        );
    }
}
