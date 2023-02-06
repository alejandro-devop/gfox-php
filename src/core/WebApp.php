<?php

namespace Alejodevop\Gfox\Core;
final class WebApp {
    private $appRoot = null;
    private function __construct() {
        # When an app is created the constructor or
        # initializer should be invoked from the root of the application
        $this->appRoot = getcwd();
    }

    public static function getInstance() : WebApp {
        static $instance = null;
        if ($instance === null)
            $instance = new WebApp();
        return $instance;
    }

    public function getAppRoot(): string {
        return $this->appRoot;
    }
}