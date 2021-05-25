<?php

namespace App\Model;

use App\Model\User;
use DateTimeImmutable;

class ResetPasswordToken
{
    private int $id;
    private User $user;
    private string $selector;
    private string $verifier;
    private string $hashedToken;
    private DateTimeImmutable $requestedAt;
    private DateTimeImmutable $expiredAt;

    public function __construct(User $user, DateTimeImmutable $expiredAt, string $selector, string $hashedToken)
    {
        $this->user = $user;
        $this->initialize($expiredAt, $selector, $hashedToken);
    }

    private function initialize(DateTimeImmutable $expiredAt, string $selector, string $hashedToken): void
    {
        $this->requestedAt = new DateTimeImmutable('now');
        $this->expiredAt = $expiredAt;
        $this->selector = $selector;
        $this->hashedToken = $hashedToken;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string Non-hashed random string used to fetch request objects from persistence
     */
    public function getSelector(): string
    {
        return $this->selector;
    }

    /**
     * @return string The hashed non-public token used to validate reset password requests
     */
    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    /**
     * The verifier is not persisted, only use for the public token.
     */
    public function setVerifier(string $verifier): string
    {
        return $this->verifier = $verifier;
    }

    /**
     * The public token consists of a concatenated random non-hashed selector string and random non-hashed verifier string.
     */
    public function getPublicToken(): string
    {
        return $this->selector . $this->verifier;
    }

    public function getRequestedAt(): DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(DateTimeImmutable $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->expiredAt->getTimestamp() <= time();
    }

    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expiredAt;
    }
}
