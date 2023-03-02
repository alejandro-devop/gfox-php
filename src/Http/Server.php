<?php 

namespace Alejodevop\Gfox\Http;
use Alejodevop\Gfox\Core\AppComponent;
use Alejodevop\Gfox\Core\Sys;

/**
 * Class handle useful information about the server where the application is running
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class Server extends AppComponent {
    /**
     * Protocol used for this request
     */
    private $protocol = 'http';
    /**
     * Current server or domain name.
     */
    private $name = '';
    /**
     * Method used for the current request to this server (Get|Post|Put|Patch|Delete)
     */
    private $method = '';
    private $uri = '/';

    public function __construct() {
        parent::__construct('AppServer');
        $this->init();
    }

    public function init() {
        $this->protocol = $this->extractProtocol();
        $this->name = $_SERVER['SERVER_NAME'];
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->uri = $_SERVER['REQUEST_URI'];
        if ($this->uri === '/favicon.ico') {
            Sys::console("Request for fav icon, abort");
            exit();
        }
    }

    /**
     * Returns a protocol lowercased
     */
    private function extractProtocol() {
        $parts = explode('/', $_SERVER['SERVER_PROTOCOL']);
        return strtolower($parts[0]);
    }

    public function getProtocol() {
        return $this->protocol;
    }

    public function getName() {
        return $this->name;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getUri() {
        return $this->uri;
    }
}