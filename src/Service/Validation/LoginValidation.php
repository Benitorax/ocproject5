<?php
namespace App\Service\Validation;

use App\Model\LoginDTO;
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

    public function validate(LoginDTO $login)
    {
        $login->messages['email'] = $this->check(self::EMAIL, $login->email, 'email');
        $login->messages['password'] = $this->check(self::PASSWORD, $login->password, 'password');


        if (!$this->hasErrorMessages($login)) {
            $login->isValid = true;
        }

        return $login;
    }

    public function hasErrorMessages(LoginDTO $login)
    {
        foreach ($login->messages as $message) {
            if ($message) {
                return true;
            }
        }

        return false;
    }
}
