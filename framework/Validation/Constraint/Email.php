<?php

namespace Framework\Validation\Constraint;

class Email extends Constraint
{
    protected string $defaultMessage = 'Invalid email address';
    protected string $messageWithLabel = 'The field "%s" has not a valid format';

    public function __construct(array $options)
    {
        parent::__construct($options);
    }

    public function validate($value): ?string
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return $this->getErrorMessage();
        }

        return null;
    }
}
