<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace TestHelpers;

class Paths
{
    public static function findModulesPath($path)
    {
        $dir = __DIR__;

        // This is used as a failsafe to avoid an endless loop
        $previousPath = '.';

        while (!is_dir($dir . DIRECTORY_SEPARATOR . $path)) {
            $dir = dirname($dir);

            if ($previousPath === $dir) {
                return false;
            }

            // We only care that when we reach the last
            // readable directory to set this in order to avoid an endless loop
            $previousPath = $dir;
        }

        return $dir . DIRECTORY_SEPARATOR . $path;
    }
}
