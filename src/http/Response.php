<?php

namespace  Alejodevop\Gfox\Http;
use Alejodevop\Gfox\Core\AppComponent;

/**
 * Class to handle the application outputed response, every type of 
 * Response should inherit from this class in order to be a valid response.
 * 
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class Response extends AppComponent{
    private $output;
    private $content;
    protected $statusCode = 200;
    public const CONTENT_JSON = 'application/json';
    protected array $headers = [
        'Content-Type' => 'text/html',
    ];
    public function __construct(mixed $responseText = "") {
        parent::__construct('AppResponse');
        $this->output = $responseText;
    }

    public function init() {}
    
    public function setHeader($header, $value): Response {
        $this->headers[$header] = $value;
        return $this;
    }

    public function prepareContent() {
        ob_start();
        echo $this->output;
        $this->content = ob_get_clean();
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function statusCode($statusCode): Response {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function send() {
        echo $this->content;
    }
}