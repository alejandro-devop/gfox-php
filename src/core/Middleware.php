<?php

namespace Alejodevop\Gfox\Core;
use Alejodevop\Gfox\Http\Response;

abstract class Middleware {
    protected ?Response $response = null;


    public function beforeRequest(): bool {
        return true;
    }
    public function run():bool {
        return true;
    }

    public function getResponse(): ?Response {
        return $this->response;
    }
}