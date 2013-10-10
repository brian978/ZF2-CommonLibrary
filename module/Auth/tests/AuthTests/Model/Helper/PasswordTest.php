<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AuthTests\Model\Helper;

use Auth\Model\Helper\Password;
use PHPUnit_Framework_TestCase;

class PasswordTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    public function testGeneratePassword()
    {
        $passwordHelper = new Password('test');
        $password       = $passwordHelper->generateHash();

        $this->assertNotEmpty($password);

        return array(
            array($password)
        );
    }

    /**
     * @dataProvider testGeneratePassword
     */
    public function testCheckPasswordMatch($passwordHash)
    {
        $passwordHelper = new Password('test');
        $passwordHash2  = $passwordHelper->processHash($passwordHash)->generateHash();

        $this->assertEquals($passwordHash2, $passwordHash);
    }

    /**
     * @dataProvider testGeneratePassword
     */
    public function testCheckPasswordMatchUsingSetPassword($passwordHash)
    {
        $passwordHelper = new Password();
        $passwordHelper->setPassword('test');

        $passwordHash2 = $passwordHelper->processHash($passwordHash)->generateHash();

        $this->assertEquals($passwordHash2, $passwordHash);
    }

    /**
     * @dataProvider testGeneratePassword
     */
    public function testPasswordMismatch($passwordHash)
    {
        $passwordHelper = new Password('test23');
        $passwordHash2  = $passwordHelper->processHash($passwordHash)->generateHash();

        $this->assertNotEquals($passwordHash, $passwordHash2);
    }
}
