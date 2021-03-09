<?php
namespace App\Form;

use App\Form\AbstractForm;

class LoginForm extends AbstractForm
{
    public string $email;
    public string $password;
    public bool $rememberme;
}
