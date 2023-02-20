<?php

namespace Alejodevop\Gfox\Http;
use Alejodevop\Gfox\Core\AppComponent;

/**
 * Note: This class is a blueprint implementation of other frameworks on how
 * to define the routes map.
 * [WARNING]: Under construction...
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class Route  extends AppComponent {
    private $routes = [];

    public function init() {}

    private function registerRoute(string $path, $resolver, $type) {
        $this->routes[] = [
            'path' => $path, 
            'resolver' => $resolver,
            'type' => $type
        ];
    }

    private function _getRoutes() {
        return $this->routes;
    }

    public static function post(string $path, $resolver) {
        self::getInstance()->registerRoute($path, $resolver, 'post');
    }
    public static function get(string $path, $resolver) {
        self::getInstance()->registerRoute($path, $resolver, 'get');
    }
    
    public static function put(string $path, $resolver) {
        self::getInstance()->registerRoute($path, $resolver, 'put');
    }
    
    public static function patch(string $path, $resolver) {
        self::getInstance()->registerRoute($path, $resolver, 'patch');
    }

    public static function delete(string $path, $resolver) {
        self::getInstance()->registerRoute($path, $resolver, 'delete');
    }

    public static function getRoutes() {
        return self::getInstance()->_getRoutes();
    }

    public static function getInstance() : Route {
        static $instance = null;
        if ($instance === null)
            $instance = new Route;
        return $instance;
    }
}