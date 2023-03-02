<?php

namespace Alejodevop\Gfox\Database;

use Alejodevop\YowlOrm\Model;

/**
 * @property mixed $id
 * @property mixed $email
 * @property mixed $password
 * @property mixed $lastLogin
 * @property mixed $firstName
 * @property mixed $lastName
 * @property mixed $isActive
 * @property mixed $createdDate
 */
class AuthUser extends Model {
    protected string $table = 'auth_user';

    public function relations() {
        return [];
    }
}