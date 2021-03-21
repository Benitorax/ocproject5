<?php

namespace Framework\Form;

use Framework\Validation\ValidationInterface;

/**
 * All forms must implement this interface which returns a validation class which implements ValidationInterface.
 */
interface FormInterface
{
    public function getValidation(): ValidationInterface;
}
