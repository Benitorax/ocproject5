<?php

namespace Framework\Validation\Constraint;

class IdenticalTo extends Constraint
{
    protected string $defaultMessage = 'Both fields should have the same value';
    protected string $messageWithLabel = 'The %s should be the same in both field';

    public function __construct(array $options)
    {
        parent::__construct($options);
    }

    public function validate($values): ?string
    {
        if (strtolower($values[0]) !== strtolower($values[1])) {
            return $this->getErrorMessage();
        }

        return null;
    }
}
