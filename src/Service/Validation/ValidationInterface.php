<?php

namespace App\Service\Validation;

use App\Form\AbstractForm;

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
