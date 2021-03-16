<?php

namespace Config\Security\RememberMe;

use DateTime;
use Exception;

class PersistentToken
{
    private string $class;
    private string $username;
    private string $series;
    private string $tokenValue;
    private DateTime $lastUsed;

    public function __construct(string $class, string $username, string $series, string $tokenValue, DateTime $lastUsed)
    {
        if (empty($class)) {
            throw new Exception('$class must not be empty.');
        }
        if ('' === $username) {
            throw new Exception('$username must not be empty.');
        }
        if (empty($series)) {
            throw new Exception('$series must not be empty.');
        }
        if (empty($tokenValue)) {
            throw new Exception('$tokenValue must not be empty.');
        }

        $this->class = $class;
        $this->username = $username;
        $this->series = $series;
        $this->tokenValue = $tokenValue;
        $this->lastUsed = $lastUsed;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getUsername(): string
    {
        return $this->username;
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
