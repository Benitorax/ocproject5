<?php

namespace Framework\Validation;

use Framework\Form\AbstractForm;

/**
 * All validation classes must implement this interface.
 */
interface ValidationInterface
{
    /**
     * Validates the form.
     */
    public function validate(AbstractForm $form): void;
}
