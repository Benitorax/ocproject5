<?php

namespace Framework\Security\Csrf;

use Framework\Session\Session;
use Framework\Security\Csrf\CsrfTokenGenerator;

class CsrfTokenManager
{
    public const NAMESPACE = 'csrf';

    private Session $session;
    private CsrfTokenGenerator $generator;
    private ?string $token = null;

    public function __construct(Session $session, CsrfTokenGenerator $generator)
    {
        $this->session = $session;
        $this->generator = $generator;
    }

    public function generateToken(): string
    {
        if (null !== $this->token) {
            return $this->token;
        }

        $this->token = $this->generator->generate();
        $this->addTokenInSession($this->token);

        return (string) $this->token;
    }

    /**
     * Adds token in session and ensures to have only the last 10 tokens in session.
     */
    public function addTokenInSession(string $token): void
    {
        $tokens = $this->getTokens();
        $tokens[] = $token;

        while (count($tokens) > 10) {
            array_shift($tokens);
        }

        $this->session->set(self::NAMESPACE, $tokens);
    }

    public function removeToken(): void
    {
        $this->session->remove(self::NAMESPACE);
    }

    public function isTokenValid(?string $token): bool
    {
        $tokens = $this->getTokens();

        foreach ($tokens as $sessionToken) {
            if (hash_equals((string) $sessionToken, $token ?? '')) {
                return true;
            }
        }

        return false;
    }

    public function getTokens(): array
    {
        if ($this->session->has(self::NAMESPACE)) {
            return $this->session->get(self::NAMESPACE);
        }

        return [];
    }
}
