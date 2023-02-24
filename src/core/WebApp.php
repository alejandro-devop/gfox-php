<?php

namespace Alejodevop\Gfox\Core;
use Alejodevop\Gfox\Http\Controller;
use Alejodevop\Gfox\Http\Request;
use Alejodevop\Gfox\Http\Response;
use Alejodevop\Gfox\Http\Router;
use Alejodevop\Gfox\Handlers\FileHandler;
use Alejodevop\Gfox\Http\Server;
use Alejodevop\YowlOrm\DBManager;

/**
 * Class to handle common application behaviours
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
final class WebApp {
    /**
     * Base dir for application
     * @var string
     */
    private ?string $appRoot;
    /**
     * Instance of the request component
     * @var Request
     */
    private Request $request;
    /**
     * Instance of the router component
     */
    private Router $router;
    /**
     * Instance of the response component
     */
    private Response $response;
    /**
     * Instance of the fileHandler component
     */
    private FileHandler $fileHandler;
    /**
     * Instance of the server component
     */
    private Server $server;
    /**
     * Instance of the controller component
     */
    private Controller $controller;

    private DBManager $dbManager;

    /**
     * List of the application available routes
     */
    private $routes = [];

    private function __construct(string $appRoot) {
        $this->appRoot = $appRoot;
        $this->init();
    }

    public function init() {
        $this->loadComponents();
        $this->loadRoutes();
    }

    /**
     * Function to load all application needed components
     */
    public function loadComponents() {
        Sys::console('Loading components', 1, __CLASS__);
        Sys::console(['Server', 'File handler', 'Router', 'Request', 'Response'], 2, __CLASS__);

        $this->server = new Server();
        $this->fileHandler = new FileHandler();
        $this->router = new Router();
        $this->request = new Request(new Response());
        $this->initializeDB();  
    }
    
    private function initializeDB() {
        Sys::console("Mounting database", 2, __CLASS__);
        $cacheDir = APP_DIR . DS . 'cache';
        DBManager::getInstance()->loadDriver('MySql', [
            'host' => 'localhost',
            'user' => 'root',
            'database' => 'iobenkyo_draft_db',
            'password' => 'JKrules',
            'port' => '3306',
            'cache_dir' => $cacheDir,
        ])->initCache(false);     
    }

    /**
     * Function to the the application server instance.
     */
    public function server(): Server {
        return $this->server;
    }

    /**
     * Lifecycle event, before start the app.
     */
    private function beforeStart() {
        Sys::console('About to start', 1, __CLASS__);
        $this->router->identifyRoute();
        $this->request->prepareRequest();
        $this->loadController();
        $this->request->checkControllerAction($this->controller);
    }

    /**
     * Function to load the application controller in memory.
     */
    private function loadController() {
        Sys::console('Loading controller', 2, __CLASS__);
        $this->controller = $this->request->loadController();

    }

    /**
     * Function to prepare the output before send the resonse
     */
    private function beforeSend() {
        Sys::console("Preparing before send the response");
        # You can capture any previous output
        ob_get_clean();
        $this->response->prepareContent();
    }

    /**
     * Function to start the application, this triggers the controller call
     */
    public function start() {
        $this->beforeStart();
        $this->response = $this->request->run($this->controller);
        $this->beforeSend();
        $this->response->send();
        Sys::endExecution();
    }

    /**
     * Function to load the application available routes.
     */
    private function loadRoutes() {
        Sys::console('Loading routes', 1, __CLASS__);
        if (!Sys::fileExists('app.cache.routes')) {
            // ToDo: Generate the file containing the routes cached...
            Sys::console('Generating routes', 2, __CLASS__);
            echo "The file does not exists";
        } else {
            Sys::console('Routes taken from the cached file', 2, __CLASS__);
            $this->routes = Sys::import('app.cache.routes');
        }
    }

    /**
     * Function to get the unique instance of the application.
     */
    public static function getInstance($appDir) : WebApp {
        static $instance = null;
        if ($instance === null) {
            Sys::console('Creating App instance', 1, __CLASS__);
            $instance = new WebApp($appDir);
        }

        return $instance;
    }

    /**
     * Function to the the application root directory
     */
    public function getAppRoot(): string {
        return $this->appRoot;
    }

    /**
     * Function to get the application routes
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Function to ghet the application router component
     */
    public function getAppRouter(): Router {
        return $this->router;
    }
}