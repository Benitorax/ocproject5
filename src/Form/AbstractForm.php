<?php
namespace App\Form;

abstract class AbstractForm
{
    public string $csrfToken = '';
    public array $errors = [];
    public bool $isValid = false;
    public bool $isSubmitted = false;
}
