<?php

namespace Framework\Validation\Constraint;

class NotBlank extends Constraint
{
    protected string $defaultMessage = 'The field should not be empty';
    protected string $messageWithLabel = 'The field "%s" should not be empty';

    public function __construct(array $options)
    {
        parent::__construct($options);
    }

    public function validate($value): ?string
    {
        if ('' === $value || null === $value || [] === $value) {
            return $this->getErrorMessage();
        }

        return null;
    }
}
