<?php

namespace Alejodevop\Gfox\Database;

use Alejodevop\YowlOrm\Model;

/**
 * @property mixed $id
 * @property mixed $userId
 * @property mixed $token
 * @property mixed $refresh
 * @property mixed $expiresAt
 * @property mixed $revoked
 */
class AuthToken extends Model {
    protected string $table = 'auth_token';

    public function relations() {
        return [];
    }
}