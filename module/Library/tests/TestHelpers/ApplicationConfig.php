<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace TestHelpers;

class ApplicationConfig
{
    protected static $config;

    /**
     * @param mixed $config
     */
    public static function setConfig($config)
    {
        self::$config = $config;
    }

    /**
     * @return mixed
     */
    public static function getConfig()
    {
        return self::$config;
    }
}
