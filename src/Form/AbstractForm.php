<?php
namespace App\Form;

abstract class AbstractForm
{
    public array $errors = [];
    public bool $isValid = false;
}
