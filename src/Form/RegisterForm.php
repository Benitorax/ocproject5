<?php
namespace App\Form;

class RegisterForm extends AbstractForm
{
    public string $email;
    public string $password1;
    public string $password2;
    public string $username;
    public bool $terms;
}
