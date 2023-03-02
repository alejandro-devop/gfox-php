<?php

namespace Alejodevop\Gfox\Core;
use Dotenv\Dotenv;


/**
 * This class contains essential methods for the running evironment.
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
final class Sys {
    /**
     * List of available aliases in the application.
     */
    private static $aliases = [];

    /**
     * Global application instance.
     * @var \Alejodevop\Gfox\Core\WebApp
     */
    private static $app = null;
    /**
     * Glocal cli application when running from the terminal.
     *
     * @var \Alejodevop\Gfox\Core\CliApp
     */
    private static $cliApp = null;
    private static $startedTime = null;
    private static $endExecution = null;
    

    /**
     * Function to create the application instance.
     */
    public static function createApp(string $appDir) {
        self::loadEnv($appDir);
        $timeZone = $_ENV['TIME_ZONE'] ?? '';
        date_default_timezone_set($timeZone);
        self::$startedTime = microtime(true);
        ob_start();
        self::console('Creating application...');
        self::loadUtilities($appDir);
        self::$app = \Alejodevop\Gfox\Core\WebApp::getInstance($appDir);
        return self::$app;
    }

    private static function loadEnv($appDir) {
        $dotenv = Dotenv::createImmutable($appDir);
        $dotenv->load();
    }

    public static function env($env, $default = null) {
        return $_ENV[$env]?? $default;
    }

    public static function cli(string $appDir, $arguments = []): CliApp {
        self::loadUtilities($appDir);
        self::loadEnv($appDir);
        self::$cliApp = CliApp::getInstance($appDir, $arguments);
        return self::$cliApp;
    }

    public static function endExecution() {
        self::$endExecution = microtime(true);
        $timeDiff = self::$endExecution - self::$startedTime;
        self::console("End execution: " . round($timeDiff, 4)) . "s";
    }

    public static function secondsToTime($s)
    {
        $h = floor($s / 3600);
        $s -= $h * 3600;
        $m = floor($s / 60);
        $s -= $m * 60;
        return $h.':'.sprintf('%02d', $m).':'.sprintf('%02d', $s);
    }

    /**
     * Function to load system utilities like globals, aliases, define additional constants
     * @param string $appDir
     */
    private static function loadUtilities($appDir) {
        self::console('Loading utilities');
        self::console(['Globals', 'App root', 'Aliases'], 1);
        
        $currentDir = dirname(__DIR__);
        require_once(__DIR__ . '/../Config/globals.php');
        if (!defined('APP_DIR')) {
            define('APP_DIR', $appDir . DS . 'app');
        }
        $aliasesPath = GFOX_ROOT . DS . 'Config' . DS . 'aliases.php';
        self::$aliases = include_once($aliasesPath);
    }

    /**
     * Function to import files using dot notation instead of specifying the whole path.
     * @param string|array $resource
     * @param boolean $single
     * @return mixed
     */
    public static function import($resource, $single = true) {
        if (is_string($resource)) {
            return self::importSingle($resource, $single);
        } else if (is_array($resource)) {
            return self::importMultiple($resource, $single);
        }
        return null;
    }

    /**
     * Function to resolve single imports
     * @param string|array $resource
     * @param boolean $single
     * @return mixed
     */
    private static function importSingle($resource, $single) {
        $path = self::resolvePath($resource, true);
        return $single? include_once($path) : include($path);
    }

    /**
     * Function to resolve multiple imports
     * Note: Due to the use of composer autoloader this is not useful for now.
     * @param string|array $resource
     * @param boolean $single
     * @return mixed
     */
    private static function importMultiple($resource, $single) {
        // Todo: Implement multiple imports
        return null;
    }

    /**
     * Function which converts a dots notation file path to a real file path.
     * @param string $path The path to be converted
     * @param boolean $isFile If the path points to a file or a folder
     * @param string $extension The extension of the file (In case $isFile is true)
     * @return string
     */
    public static function resolvePath($path, $isFile = true, $extension = 'php') {
        $parts = explode('.', $path);
        
        if (count($parts) === 1) {
            return key_exists($path, self::$aliases)? self::$aliases[$path] : '';
        }

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

    /**
     * Function to check if a given path to a file exists (Works with folders too)
     * @param string $path
     * @param string $extension
     * @return boolean
     */
    public static function fileExists($path, $extension = 'php') {
        $path = self::resolvePath($path, true, $extension);
        return file_exists($path);
    }

    /**
     * Function to print variables content
     * ToDo: Enhance this function.
     * @param mixed $content
     */
    public static function dump(...$content) {
        echo '<pre>';
        var_dump($content);
        echo '</pre>';
        exit();
    }

    /**
     * Function to get the application instance.
     */
    public static function app():\Alejodevop\Gfox\Core\WebApp {
        return self::$app;
    }

    public static function cliApp():CliApp {
        return self::$cliApp;
    }

    /**
     * Function to log to the console
     * @param mixed $log
     * @param int $levels How many identation levels
     * @param string $class The class name which reports the log
     */
    public static function console($log, $levels = 0, $class = __CLASS__) {
        $tabs = str_repeat("\t", $levels);
        $colorOpen = "\033[32m";
        $colorClose = "\033[0m";
        if (is_array($log)) {
            foreach($log as $message) {
                error_log("$colorOpen $tabs|... [$class]: $message $colorClose");
            }
        } else {
            error_log("$colorOpen $tabs|... [$class]: $log $colorClose");
        }
    }

    /**
     * Function to convert a path to a namespace
     * @param string $path
     */
    public static function toNameSpace($path) {
        $path = array_map(fn ($item) => ucfirst($item), explode('.', $path));
        $result = implode("\\", $path);
        return "\\" . $result;
    }
}