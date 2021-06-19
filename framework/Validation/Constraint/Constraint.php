<?php

namespace Framework\Validation\Constraint;

abstract class Constraint implements ConstraintInterface
{
    protected ?string $customMessage = null;
    protected string $defaultMessage = 'The field is not valid';
    protected string $messageWithLabel = 'The field "%s" is not valid';
    protected ?string $label = null;
    protected array $options = [];

    public function __construct(array $options)
    {
        foreach ($options as $name => $value) {
            if ('message' === $name) {
                $this->customMessage = $value;
                continue;
            }

            $this->$name = $value;
        }
    }

    /**
     * Returns an error message.
     */
    public function getErrorMessage(): string
    {
        if (null !== $this->customMessage) {
            return $this->customMessage;
        }

        if (null !== $this->label) {
            return sprintf($this->messageWithLabel, $this->label);
        }

        return $this->defaultMessage;
    }
}
