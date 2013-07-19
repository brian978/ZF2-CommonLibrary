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
        $string = preg_replace('/([\W])/', '-', $string);
        $pieces = explode('-', $string);

        // Removing the starting dashes
        if(empty($pieces[0])) {
            array_shift($pieces);
        }

        if(empty($pieces[count($pieces) - 1])) {
            array_pop($pieces);
        }

        $string = strtolower(implode('-', $pieces));
        $string = preg_replace('/([-]+)/', '-', $string);

        return $string;
    }
}
