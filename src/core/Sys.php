<?php

namespace Alejodevop\Gfox\Core;

final class Sys {
    private static $aliases = [];

    /**
     * Summary of app
     * @var \Alejodevop\Gfox\Core\WebApp
     */
    private static $app = null;

    public static function createApp() {
        self::loadUtilities();
        self::$app = \Alejodevop\Gfox\Core\WebApp::getInstance();
        return self::$app;
    }

    private static function loadUtilities() {
        $currentDir = dirname(__DIR__);
        require_once(__DIR__ . '/../config/globals.php');
        $aliasesPath = GFOX_ROOT . DS . 'config' . DS . 'aliases.php';
        self::$aliases = include_once($aliasesPath);
    }

    public static function import($resource) {
        self::importSingle($resource);
        return null;
    }

    private static function importSingle($resource) {
        $path = self::resolvePath($resource, true);
    }

    public static function resolvePath($path, $isFile = true, $extension = 'php') {
        $parts = explode('.', $path);
        $lastElement = array_pop($parts);
        $initialElement = array_shift($parts);
        $output = '';
        if (key_exists($initialElement, self::$aliases)) {
            $output = self::$aliases[$initialElement];
        }

        if (count($parts) > 0) {
            $output .= DS . implode(DS, $parts);
        }

        return $output . DS . ($isFile? "$lastElement.$extension" : $lastElement);
    }
}