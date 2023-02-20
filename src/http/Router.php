<?php

namespace Alejodevop\Gfox\Http;

use Alejodevop\Gfox\Core\AppComponent;
use Alejodevop\Gfox\Core\Sys;
use Alejodevop\Gfox\Exception\HttpRouteNotFoundExeption;

/**
 * This class handles the application routing, it determines
 * what is the current application route and if this is defined
 * in the routes file.
 * 
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class Router extends AppComponent{
    private $applicationRoute = null;

    public function init() {}

    public function __construct() {
        parent::__construct('AppRouter');
    }

    /**
     * This function extract information from the uri to construct detect the
     * current controller and action requested by the user.
     */
    private function explodeUri() {
        $uri = Sys::app()->server()->getUri();
        $parts = explode('/', $uri);
        $controller = $parts[1] ?? null;
        $action = $parts[2]?? null;
        unset($parts[0], $parts[1], $parts[2]);
        return [
            'action' => $action ?? 'index',
            'controller' => $controller ?? 'home',
            'params' => array_values($parts),
        ];
    }

    /**
     * This function identify the current url requested by the user.
     */
    public function identifyRoute() {
        Sys::console('Identifying initial route', 1, __CLASS__);
        $uriInfo = $this->explodeUri();
        $appRoutes = Sys::app()->getRoutes();

        ['controller' => $reqCtrlr, 'action' => $reqActn ] = $uriInfo;
        $currentRequestType = Sys::app()->server()->getMethod();
        $currentRoute = null;

        foreach($appRoutes as $route) {
            ['action' => $action, 'controllerKey' => $controller, 'type' => $type] = $route;
            if ($action === $reqActn && $controller === $reqCtrlr && $currentRequestType === $type) {
                $currentRoute = $route;
                break;
            }
        }
        
        if (is_null($currentRoute)) {
            throw new HttpRouteNotFoundExeption("Route: $reqCtrlr/$reqActn not Found for Method '$currentRequestType'");
        }

        $this->applicationRoute = $currentRoute;
    }

    /**
     * Returns the current application route found in the routes file.
     * @return array
     */
    public function getApplicationRoute() {
        return $this->applicationRoute;
    }
}