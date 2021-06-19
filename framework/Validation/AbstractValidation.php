<?php

namespace Framework\Validation;

use Framework\DAO\DAO;
use Framework\Security\Csrf\CsrfTokenManager;
use Framework\Validation\ValidationInterface;
use Framework\Validation\Constraint\Unique;

abstract class AbstractValidation implements ValidationInterface
{
    private DAO $dao;
    private CsrfTokenManager $csrfTokenManager;

    public function __construct(DAO $dao, CsrfTokenManager $csrfTokenManager)
    {
        $this->dao = $dao;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Validates a value by calling the Constraint's methods.
     *
     * @param array $constraints
     * @param mixed $value
     */
    public function check($constraints, $value): ?string
    {
        foreach ($constraints as $class => $options) {
            $constraint = new $class($options);
            if ($constraint instanceof Unique) {
                $constraint->setDAO($this->dao);
            }

            $message = $constraint->validate($value);
            if (null !== $message) {
                return $message;
            }
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
