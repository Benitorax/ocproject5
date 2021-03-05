<?php
namespace App\Service;

use App\Model\User;

class PasswordEncoder
{
    const OPTIONS = [
        'cost' => 12
    ];
    
    public function encode(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, self::OPTIONS);
    }

    public function isPasswordValid(User $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }
}
