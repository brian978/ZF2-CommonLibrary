<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Log;

use Zend\Log\LoggerAwareInterface as ZendLoggerAwareInterface;
use Zend\Log\LoggerInterface;

interface LoggerAwareInterface extends ZendLoggerAwareInterface
{
    /**
     *
     * @return LoggerInterface
     */
    public function getLogger();
}
