<?php

namespace Framework\Validation\Constraint;

class IsTrue extends Constraint
{
    protected string $defaultMessage = 'The field must be true';
    protected string $messageWithLabel = 'The field "%s" must be true';

    public function __construct(array $options)
    {
        parent::__construct($options);
    }

    public function validate($value): ?string
    {
        if (null !== $value && (bool) $value !== true) {
            return $this->getErrorMessage();
        }

        return null;
    }
}
