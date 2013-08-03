<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class FriendlyUrl extends AbstractHelper
{
    public function __invoke($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/([\W-]+)/', '-', $string);
        $string = substr($string, 1);
        $string = substr($string, -strlen($string), -1);

        return $string;
    }
}
