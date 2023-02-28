<?php

namespace Alejodevop\Gfox\Auth;
use Alejodevop\Gfox\Core\Sys;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    public static function genPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function comparePassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public static function genToken($payload = []) {
        $time = time();
        $expDays = Sys::env('TOKEN_EXPIRATION_DAYS', 30);
        $expiration = 60 * 60 * 24 * $expDays;
        $expTime = $time + $expiration;
        $tokenInfo = [
            'iat' => $time,
            'exp' => $time + $expiration,
            'data' => $expTime
        ];
        $secret = Sys::env('JWT_SECRET', 'lCeUjA007T');
        return [
            'token' => JWT::encode($tokenInfo, $secret, 'HS256'),
            'expiration' => $expTime
        ];
    }

    public static function decodeToken($token) {
        $secret = Sys::env('JWT_SECRET', 'lCeUjA007T');
        return JWT::decode($token, new Key($secret,'HS256'));
    }
}