<?php
namespace App\Model;

class LoginDTO
{
    public string $email;
    public string $password;
    public bool $rememberme;

    public array $messages = [];
    public bool $isValid = false;
}
