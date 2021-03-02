<?php
namespace App\Service\Validation;

use App\Model\UserDTO;
use App\Service\Validation\Validation;

class UserValidation extends Validation
{
    const EMAIL = [
        ['notBlank'],
        ['minLength', 8],
        ['maxLength', 50],
        ['unique', 'user:email']
    ];
    const PASSWORD1 = [
        ['notBlank'],
        ['minLength', 6],
        ['maxLength', 50]
    ];
    const USERNAME = [
        ['notBlank'],
        ['minLength', 3],
        ['maxLength', 50],
        ['unique', 'user:username']
    ];
    const TERMS = [
        ['checkbox', true]
    ];

    public function validate(UserDTO $user)
    {
        $user->messages['email'] = $this->check(self::EMAIL, $user->email, 'email');
        $user->messages['password1'] = $this->check(self::PASSWORD1, $user->password1, 'password');
        $user->messages['username'] = $this->check(self::USERNAME, $user->username, 'username');
        $user->messages['terms'] = $this->check(self::TERMS, $user->terms, 'terms of use');

        if(!$user->messages['password1']) {
            $user->messages['password2'] = $this->checkIdentical($user->password1, $user->password2, 'password');
        }

        if(!$this->hasErrorMessages($user)) {
            $user->isValid = true;
        }

        return $user;
    }

    public function hasErrorMessages(UserDTO $user)
    {
        foreach($user->messages as $message) {
            if($message) {
                return true;
            }
        }

        return false;
    }
}