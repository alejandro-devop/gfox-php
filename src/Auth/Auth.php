<?php

namespace Alejodevop\Gfox\Auth;
use Alejodevop\Gfox\Core\Sys;
use Alejodevop\Gfox\Database\AuthToken;
use Alejodevop\Gfox\Database\AuthUser;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {

    public static function login(array $payload = []) {
        $email = $payload['email']?? '';
        $password = $payload['password']?? '';
        if ($email === '' || $password === '') {
            return false;
        }
        $user = AuthUser::search()
            ->equals('email', $email)
            ->first();
        if (!is_null($user) && $user instanceof AuthUser) {
            if (self::comparePassword($password, $user->password)) {
                $token = Auth::genToken([
                    'email' => $email,
                    'userId' => $user->id,
                ]);
                $user->lastLogin = date('Y-m-d H:i:s');
                $user->save();

                $previousToken = AuthToken::search()
                    ->equals('user_id', $user->id)
                    ->equals('revoked', '0')
                    ->first();
                if ($previousToken !== NULL && $previousToken instanceof AuthToken) {
                    # $previousToken->revoked = 1;
                    # $previousToken->save();
                    // $previousToken->delete();
                }
                $newToken = new AuthToken();
                $newToken->userId = $user->id;
                $newToken->token = $token['token']?? '';
                $newToken->refresh = " "; # Todo: Implement refresh system.
                $newToken->expiresAt = date('Y-m-d H:i:s', $token['expiration']);
                $newToken->revoked = false;
                $newToken->save();
                return [
                    'token' => $token,
                    'user' => $user
                ];
            } else {
                return false;
            }
        }
        exit();
    }

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