<?php
namespace App\Model;

class UserDTO
{
    public string $email;
    public string $password1;
    public string $password2;
    public string $username;
    public bool $terms;

    public array $messages = [];
    public bool $isValid = false;
}
