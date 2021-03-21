<?php

namespace Framework\Security\Encoder;

use Framework\Security\User\UserInterface;

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

    public function isPasswordValid(UserInterface $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }
}
