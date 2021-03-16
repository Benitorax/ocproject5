<?php

namespace App\Service;

use App\Model\User;

class PasswordEncoder
{
    private const OPTIONS = [
        'cost' => 12
    ];

    /**
     * @return false|string
     */
    public function encode(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT, self::OPTIONS);
    }

    public function isPasswordValid(User $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }
}
