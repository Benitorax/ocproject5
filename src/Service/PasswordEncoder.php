<?php
namespace App\Service;

class PasswordEncoder
{
    const OPTIONS = [
        'cost' => 12
    ];
    
    public function encode(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, self::OPTIONS);
    }

    public function checkPassword($password, $encodedPassword): bool
    {
        if($this->encode($password) === $encodedPassword) {
            return true;
        } else {
            return false;
        }
    }
}