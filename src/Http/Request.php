<?php
namespace  Alejodevop\Gfox\Http;

use Alejodevop\Gfox\Auth\AuthenticatedMiddleWare;
use Alejodevop\Gfox\Core\AppComponent;
use Alejodevop\Gfox\Core\Middleware;
use Alejodevop\Gfox\core\Sys;
use Alejodevop\Gfox\Database\AuthUser;
use Alejodevop\Gfox\Exception\HttpActionNotExistException;
use Alejodevop\Gfox\Exception\HttpNotValidResponseException;
use Alejodevop\Gfox\Exception\SysFilePathNotFoundExeption;

/**
 * Class to handle the application requests
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class Request extends AppComponent{
    /**
     * Type of the request (get|post|put|delete|patch)
     * @var string
     */
    private ?string $type;
    private AuthUser $user;
    private $host;
    private $cacheControl;
    private $browser;
    private $platform;
    private $authType;
    private $authToken;
    private $userAgent;
    private $accept;
    private $body = '';
    /**
     * Name of the controller to be invoked
     * @var string
     */
    private ?string $controller;
    private ?array $middlewares;
    private array $registeredMiddlewares = [
        'auth' => AuthenticatedMiddleWare::class
    ];
    /**
     * Name of the action to be invoked
     * @var string
     */
    private ?string $action;
    /**
     * Response instance to prepare the output
     * @var Response
     */
    private Response $response;

    public function __construct(Response $response) {
        $this->response = $response;
        parent::__construct("AppRequest");
    }

    /**
     * Function to prepare the request, it inspects the current URI to determine
     * which controller will be loaded and which action will be called.
     */
    public function prepareRequest() {
        Sys::console('Preparing the request', 1, __CLASS__);
        $currentRoute = Sys::app()->getAppRouter()->getApplicationRoute();
        [
            'type' => $this->type, 
            'controller' => $this->controller, 
            'action' => $this->action
        ] = $currentRoute;
        $this->middlewares = $currentRoute['middlewares'] ?? null;
    }

    public function init() {
        $this->extractHeaders();
        $this->body = file_get_contents('php://input');
    }

    public function get($data) {
        echo "About to get a property: $data";
        exit();
    }

    public function body() {
        $data = json_decode($this->body, true);
        return $data;
    }

    private function extractHeaders() {
        $headers = getallheaders();
        $this->host = $headers['Host']?? '';
        $this->cacheControl = $headers['Cache-Control']?? '';
        $auth = explode(' ', $headers['Authorization']?? '');
        $this->authType = $auth[0] ?? '';
        $this->authToken = $auth[1]?? '';
    }

    public function getAuthInfo() {
        return [
            'authType' => $this->authType,
            'authToken' => $this->authToken
        ];
    }

    public function getAuthToken() {
        return $this->authToken;
    }

    public function getAuthType() {
        return $this->authType;
    }

    /**
     * Getter for type of request
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Getter for the controller name
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Getter for the action name.
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Function to load the current controller instance
     * @return Controller
     */
    public function loadController(): Controller {
        $path = "app.Controller.$this->controller";
        if (!Sys::fileExists($path)) {
            throw new SysFilePathNotFoundExeption("The file: $path does not exists");
        }
        Sys::import($path);
        $namespace = Sys::toNameSpace($path);
        $ID = substr($path, strrpos($path, '.') + 1);
        $object = new $namespace($ID, $this);

        if (!$object instanceof AppComponent) {
            throw new \Exception("Not a valid app component $namespace");
        }
        if (!$object instanceof Controller) {
            throw new \Exception("Not a valid app Controller $namespace");
        }

        $object->init();
        return $object;
    }

    /**
     * Function to check if the controller action that is being prepared
     * is a valid function inside the controller
     */
    public function checkControllerAction(Controller $controller) {
        Sys::console("Checking the controller action [$this->action]", 2, __CLASS__);
        if (!method_exists($controller, $this->action)) {
            throw new HttpActionNotExistException("The action $this->action does not exists");
        }
    }

    protected function loadMiddleware($middlewareName): Middleware {
        if (!key_exists($middlewareName, $this->registeredMiddlewares)) {
            throw new \Exception("Middlewrare $middlewareName  does not exists");
        }
        $className = $this->registeredMiddlewares[$middlewareName];
        $middleware = new $className();
        return $middleware;
    }

    /**
     * Function to execute the controller and capture the respnose send by this execution.
     * @return Response
     */
    public function run(Controller $controller): Response {
        Sys::console("Running the request");
        if (!is_null($this->middlewares)) {
            $middlewareResponse = null;
            foreach($this->middlewares as $middlewareName) {
                $middleware = $this->loadMiddleware($middlewareName);
                $valid = $middleware->beforeRequest();
                if (!$valid) {
                    $middlewareResponse = $middleware->getResponse();
                    break;
                }
            }
            if (!is_null($middlewareResponse) && $middlewareResponse instanceof Response) {
                return $middlewareResponse;
            }
        }

        $response = call_user_func_array([$controller, $this->action], []);
        if (!$response instanceof Response) {
            throw new HttpNotValidResponseException("Not a valid response");
        }
        return $response;
    }
}