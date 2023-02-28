<?php

namespace Alejodevop\Gfox\Http;
use Alejodevop\Gfox\Http\Response;

class JSONResponse extends Response {
    public function __construct(mixed $response) {
        $this->setHeader('Content-Type', 'application/json');
        if (is_array($response)) {
            $content = json_encode($response);
            parent::__construct($content);
        } else {
            throw new \Exception("Invalid json content");
        }
    }
}