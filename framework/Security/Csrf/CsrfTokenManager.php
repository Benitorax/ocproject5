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
        if ($this->token !== null) {
            return $this->token;
        }
        $this->token = $this->generator->generate();
        $this->session->set(self::NAMESPACE, $this->token);

        return $this->token;
    }

    public function removeToken(): void
    {
        $this->session->remove(self::NAMESPACE);
    }

    public function isTokenValid(?string $token): bool
    {
        $sessionToken = $this->getToken();

        if (hash_equals((string) $sessionToken, $token ?? '')) {
            return true;
        }

        return false;
    }

    public function getToken(): ?string
    {
        if ($this->session->has(self::NAMESPACE)) {
            return $this->session->get(self::NAMESPACE);
        }

        return null;
    }
}
