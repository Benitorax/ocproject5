<?php

namespace Framework\Security\Encoder;

use App\Model\User;

class PasswordEncoder
{
    private const OPTIONS = [
        'cost' => 12
    ];

    /**
     * Hash the password.
     *
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
