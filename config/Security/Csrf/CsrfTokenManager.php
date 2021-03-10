<?php
namespace Config\Security\Csrf;

use Config\Session\Session;
use Config\Security\Csrf\CsrfTokenGenerator;

class CsrfTokenManager
{
    private $session;
    private $token;
    const NAMESPACE = 'csrf';

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function generateToken(): string
    {
        if(!empty($this->token)) {
            return $this->token;
        }
        
        $this->token = CsrfTokenGenerator::generate();
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

        if(hash_equals($sessionToken, $token ?? '')) {
            return true;
        }

        return false;
    }

    public function getToken(): ?string
    {
        if($this->session->has(self::NAMESPACE)) {
            return $this->session->get(self::NAMESPACE);
        }

        return null;
    }
}