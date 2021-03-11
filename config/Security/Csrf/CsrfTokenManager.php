<?php
namespace Config\Security\Csrf;

use Config\Session\Session;
use Config\Security\Csrf\CsrfTokenGenerator;

class CsrfTokenManager
{
    private $session;
    private $token = null;
    const NAMESPACE = 'csrf';
    private $generator;
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

    public function removeToken()
    {
        $this->session->remove(self::NAMESPACE);
    }

    public function isTokenValid(?string $token): bool
    {
        $sessionToken = $this->getToken(self::NAMESPACE);
        
        if (hash_equals($sessionToken, $token ?? '')) {
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
