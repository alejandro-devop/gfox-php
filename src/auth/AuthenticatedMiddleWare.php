<?php

namespace Alejodevop\Gfox\Auth;
use Alejodevop\Gfox\Core\Middleware;
use Alejodevop\Gfox\Core\Sys;
use Alejodevop\Gfox\Database\AuthToken;
use Alejodevop\Gfox\Http\JSONResponse;

class AuthenticatedMiddleWare extends Middleware {
    public function beforeRequest(): bool {
        $valid = false;
        [
            'authType' => $authType, 
            'authToken' => $authToken
        ] = Sys::app()->getRequest()->getAuthInfo();
        if ($authType === 'Bearer' && $authToken !== '') {
            $tokenInDB = AuthToken::search()
                ->equals('token', $authToken)
                ->first();
            if (!is_null($tokenInDB) && $tokenInDB instanceof AuthToken) {
                $exp = $tokenInDB->expiresAt;
                $expTime = strtotime($exp);
                $current = time();
                if ($current < $expTime) {
                    $valid = true;
                } else {
                    $this->response = (new JSONResponse([
                        'error' => true,
                        'message' => 'The token has expired'
                    ]))
                    ->statusCode(401);
                }
            } else {
                $this->response = (new JSONResponse([
                    'error' => true,
                    'message' => 'Invalid token'
                ]))
                ->statusCode(401);
            }
        }
        return $valid;
    }
}