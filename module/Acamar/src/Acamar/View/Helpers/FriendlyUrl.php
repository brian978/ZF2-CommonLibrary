<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class FriendlyUrl extends AbstractHelper
{
    public function __invoke($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/([\W-]+)/', '-', $string);

        // Removing the first dash (if there is one)
        if (strpos($string, '-') === 0) {
            $string = substr($string, 1);
        }

        // Removing the last dash (if there is one)
        if (strpos($string, '-', strlen($string) - 1)) {
            $string = substr($string, -strlen($string), -1);
        }

        return $string;
    }
}
