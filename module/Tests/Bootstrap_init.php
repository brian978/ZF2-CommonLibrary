<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

// We need to change the dir to the root so the configs would work properly
chdir(dirname(dirname(__DIR__)));

require 'Bootstrap.php';

\Tests\Bootstrap::init();
