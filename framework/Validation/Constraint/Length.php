<?php

namespace Framework\Validation\Constraint;

class Length extends Constraint
{
    protected string $maxMessage = 'The field should not contain more than %d characters';
    protected string $maxMessageWithLabel = 'The field "%s" should not contain more than %d characters';
    protected string $minMessage = 'The field should contain at least %d characters';
    protected string $minMessageWithLabel = 'The field "%s" should contain at least %d characters';
    protected string $exactMessage = 'The field should have exactly %d characters';
    protected string $exactMessageWithLabel = 'The field "%s" should have exactly %d characters';
    protected ?int $min = null;
    protected ?int $max = null;

    public function __construct(array $options)
    {
        parent::__construct($options);
    }

    public function validate($value): ?string
    {
        $length = mb_strlen($value);

        if (null !== $this->min && $length < $this->min) {
            return $this->min === $this->max ? $this->getExactMessage() : $this->getMinMessage();
        }

        if (null !== $this->max && $length > $this->max) {
            return $this->min === $this->max ? $this->getExactMessage() : $this->getMaxMessage();
        }

        return null;
    }

    public function getMaxMessage(): string
    {
        if (null !== $this->label) {
            return sprintf($this->maxMessageWithLabel, $this->label, $this->max);
        }

        return sprintf($this->maxMessage, $this->max);
    }

    public function getMinMessage(): string
    {
        if (null !== $this->label) {
            return sprintf($this->minMessageWithLabel, $this->label, $this->min);
        }

        return sprintf($this->minMessage, $this->min);
    }

    public function getExactMessage(): string
    {
        if (null !== $this->label) {
            return sprintf($this->exactMessageWithLabel, $this->label, $this->min);
        }

        return sprintf($this->exactMessage, $this->min);
    }
}
