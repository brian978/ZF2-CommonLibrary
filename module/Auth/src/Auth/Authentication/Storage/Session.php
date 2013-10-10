<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Authentication\Storage;

use Zend\Authentication\Storage\Session as ZendAuthSessionStorage;

class Session extends ZendAuthSessionStorage
{
    /**
     * @return \Zend\Session\Container
     */
    public function getSessionContainer()
    {
        return $this->session;
    }
}
