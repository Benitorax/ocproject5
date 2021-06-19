<?php

namespace Framework\Validation\Constraint;

class IsFalse extends Constraint
{
    protected string $defaultMessage = 'The field must be false';
    protected string $messageWithLabel = 'The field "%s" must be false';

    public function __construct(array $options)
    {
        parent::__construct($options);
    }

    public function validate($value): ?string
    {
        if (null !== $value && (bool) $value !== false) {
            return $this->getErrorMessage();
        }

        return null;
    }
}
