<?php
namespace App\Service\Validation;

use App\Form\AbstractForm;
use App\Service\Validation\Constraint;

abstract class Validation
{
    private $constraint;

    public function __construct(Constraint $constraint)
    {
        $this->constraint = $constraint;
    }

    public function check(array $constraints, $value, $name = null)
    {
        foreach ($constraints as $constraint) {
            $error = $this->constraint->validate($constraint, $value, $name);

            if (!empty($error)) {
                return $error;
            }
        }

        return null;
    }

    public function checkIdentical($value1, $value2, $name = null)
    {
        $error = $this->constraint->identical($value1, $value2, $name);
        
        if (!empty($error)) {
            return $error;
        }

        return null;
    }

    public function hasErrorMessages(AbstractForm $form)
    {
        foreach ($form->errors as $error) {
            if (!empty($error)) {
                return true;
            }
        }

        return false;
    }
}
