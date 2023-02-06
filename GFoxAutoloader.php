<?php

namespace Alejodevop\Gfox;

final class GFoxAutoloader {
    private static $packageName = "Alejodevop\\Gfox";
    /**
     * Initializes the autoloader for gfox
     */
    public static function initAutoload() {
        self::defineConstants();
        spl_autoload_register('\Alejodevop\Gfox\GFoxAutoloader::autoload');
    }

    /**
     * Define constants used by the autoload
     */
    private static function defineConstants() {
        define('GFOX_ROOT', realpath(__DIR__));
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
    }
    /**
     * Function which will be triggered when class call fails
     */
    private static function autoload(string $classPath) {
        $classPath = str_replace(self::$packageName, '', $classPath);
        $parts = explode("\\", $classPath);
        $className = array_pop($parts);
        $parts = array_map(function ($part) {
            return lcfirst($part);
        }, $parts);
        $path = GFOX_ROOT . DS;
        $path .= "\\src" . implode("\\", $parts) . "\\$className";
        $path = str_replace('\\', DS, $path). '.php';

        if (file_exists($path)) {
            require_once $path;
        }
    }

}