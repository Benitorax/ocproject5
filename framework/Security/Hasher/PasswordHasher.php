<?php

namespace Framework\Security\Hasher;

use Framework\Security\User\UserInterface;

class PasswordHasher
{
    private const OPTIONS = [
        'cost' => 12
    ];

    /**
     * Hash the password.
     *
     * @return false|string
     */
    public function hash(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT, self::OPTIONS);
    }

    public function isPasswordValid(UserInterface $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }
}
