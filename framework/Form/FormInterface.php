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

    /**
     * If your form can be hydrated with a variable you can add this method:
     * 
     * public function hydrateForm(Class|Array $variable): self
     * 
     * The variable and type-hint can be anything, e.g.:
     * public function hydrateForm(Post $post): self
     */

    /**
     * If you set hydrateForm method you may need this method:
     * 
     * public function getData: Class|Array
     * 
     * The return type-hint can be anything, e.g.:
     * public function getData(): Post
    */
}
