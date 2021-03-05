<?php
namespace App\Service\Validation;

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

            if ($error) {
                return $error;
            }
        }

        return null;
    }

    public function checkIdentical($value1, $value2, $name = null)
    {
        $error = $this->constraint->identical($value1, $value2, $name);
        if ($error) {
            return $error;
        }

        return null;
    }
}
