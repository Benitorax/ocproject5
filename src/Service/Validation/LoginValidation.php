<?php
namespace App\Service\Validation;

use App\Form\LoginForm;
use App\Service\Validation\Validation;

class LoginValidation extends Validation
{
    const EMAIL = [
        ['notBlank'],
        ['minLength', 8],
        ['maxLength', 50],
    ];
    const PASSWORD = [
        ['notBlank'],
        ['minLength', 6],
        ['maxLength', 50]
    ];

    public function validate(LoginForm $form): void
    {
        $form->errors['email'] = $this->check(self::EMAIL, $form->email, 'email');
        $form->errors['password'] = $this->check(self::PASSWORD, $form->password, 'password');
        $form->errors['csrf'] = $this->checkCsrfToken($form->csrfToken);

        if (!$this->hasErrorMessages($form)) {
            $form->isValid = true;
        }
    }
}
