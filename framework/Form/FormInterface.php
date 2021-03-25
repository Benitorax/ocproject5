<?php

namespace Framework\Form;

use Framework\Validation\ValidationInterface;

/**
 * All forms must implement this interface which returns a validation class which implements ValidationInterface.
 */
interface FormInterface
{
    /**
     * Returns the validation object which validates the form.
     */
    public function getValidation(): ValidationInterface;

    /**
     * Returns a new instance of the form.
     */
    public function newInstance(): self;
}
