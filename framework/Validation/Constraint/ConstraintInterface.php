<?php

namespace Framework\Validation\Constraint;

interface ConstraintInterface
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value
     * @return null|string
     */
    public function validate($value);
}
