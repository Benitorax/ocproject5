<?php

namespace Framework\Validation;

use Framework\Validation\Constraint;
use Framework\Security\Csrf\CsrfTokenManager;
use Framework\Validation\ValidationInterface;

abstract class Validation implements ValidationInterface
{
    private Constraint $constraint;
    private CsrfTokenManager $csrfTokenManager;

    public function __construct(Constraint $constraint, CsrfTokenManager $csrfTokenManager)
    {
        $this->constraint = $constraint;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Validates a value by calling the Constraint's methods.
     *
     * @param bool|string|int $value
     * @param string $name the name of the field for the error message
     */
    public function check(array $constraints, $value, string $name = null): ?string
    {
        foreach ($constraints as $constraint) {
            $error = $this->constraint->validate($constraint, $value, $name);

            if (!empty($error)) {
                return $error;
            }
        }

        return null;
    }

    /**
     * Checks if value1 is equal to value2.
     * @param string $name the name of the field for the error message
     */
    public function checkIdentical(string $value1, string $value2, string $name = null): ?string
    {
        $error = $this->constraint->identical($value1, $value2, $name);

        if (!empty($error)) {
            return $error;
        }

        return null;
    }

    /**
     * Checks if the csrf token is valid.
     */
    public function checkCsrfToken(string $token): ?string
    {
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            return 'The CSRF token is invalid. Please try to resubmit the form.';
        }
        return null;
    }
}
