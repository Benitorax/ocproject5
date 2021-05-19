<?php

namespace Framework\Security\RememberMe;

use DateTime;
use Exception;

/**
 * This class is persisted in database and allow to check if a remember me cookie is valid.
 */
class PersistentToken
{
    private string $class;

    /**
     *  @var mixed
     */
    private $identifier;

    private string $series;
    private string $tokenValue;
    private DateTime $lastUsed;

    /**
     * @param mixed $identifier
     */
    public function __construct(string $class, $identifier, string $series, string $tokenValue, DateTime $lastUsed)
    {
        if (empty($class)) {
            throw new Exception('$class must not be empty.');
        }
        if ('' === $identifier) {
            throw new Exception('$identifier must not be empty.');
        }
        if (empty($series)) {
            throw new Exception('$series must not be empty.');
        }
        if (empty($tokenValue)) {
            throw new Exception('$tokenValue must not be empty.');
        }

        $this->class = $class;
        $this->identifier = $identifier;
        $this->series = $series;
        $this->tokenValue = $tokenValue;
        $this->lastUsed = $lastUsed;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSeries(): string
    {
        return $this->series;
    }

    public function getTokenValue(): string
    {
        return $this->tokenValue;
    }

    public function getLastUsed(): DateTime
    {
        return $this->lastUsed;
    }
}
