<?php
namespace Config\Security\RememberMe;

use DateTime;
use Exception;
use App\Model\User;
use App\DAO\UserDAO;
use Config\Cookie\Cookie;
use Config\Request\Request;
use Config\Security\TokenStorage;

class RememberMeManager
{
    public const COOKIE_DELIMITER = ':';
    public const COOKIE_ATTR_NAME = 'remember_me_cookie';
    public const COOKIE_NAME = 'REMEMBERME';

    protected $options = [
        'name' => 'REMEMBERME',
        'lifetime' => 31536000,
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'samesite' => null,
    ];

    private $rememberMeDAO;
    private $userDAO;
    private $tokenStorage;

    public function __construct(RememberMeDAO $rememberMeDAO, UserDAO $userDAO, TokenStorage $tokenStorage)
    {
        $this->rememberMeDAO = $rememberMeDAO;
        $this->userDAO = $userDAO;
        $this->tokenStorage = $tokenStorage;
    }

    public function processAutoLoginCookie(array $cookieParts, Request $request)
    {
        $this->cancelCookie($request);

        if (2 !== \count($cookieParts)) {
            throw new Exception('The cookie is invalid.');
        }

        [$series, $tokenValue] = $cookieParts;
        $persistentToken = $this->rememberMeDAO->loadTokenBySeries($series);

        if (empty($persistentToken)) {
            throw new Exception('No token found.');
        }

        if (!hash_equals($persistentToken->getTokenValue(), $tokenValue)) {
            throw new Exception('This token was already used. The account is possibly compromised.');
        }

        if ($persistentToken->getLastUsed()->getTimestamp() + $this->options['lifetime'] < time()) {
            throw new Exception('The cookie has expired.');
        }

        $tokenValue = base64_encode(random_bytes(64));
        $this->rememberMeDAO->updateToken($series, $tokenValue, new DateTime());
        $request->attributes->set(
            self::COOKIE_ATTR_NAME,
            new Cookie(
                $this->options['name'],
                $this->encodeCookie([$series, $tokenValue]),
                time() + $this->options['lifetime'],
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'] ?? $request->isSecure(),
                $this->options['httponly'],
                false,
                $this->options['samesite']
            )
        );

        return $this->userDAO->getOneBy(['username' => $persistentToken->getUsername()]);
    }

    public function autoLogin(Request $request)
    {
        if (($cookie = $request->attributes->get(self::COOKIE_ATTR_NAME)) && null === $cookie->getValue()) {
            return null;
        }

        if (null === $cookie = $request->cookies->get($this->options['name'])) {
            return null;
        }

        $cookieParts = $this->decodeCookie($cookie);
        $user = $this->processAutoLoginCookie($cookieParts, $request);

        if (!$user instanceof User) {
            throw new Exception('processAutoLoginCookie() must return a User class.');
        }

        return new RememberMeToken($user);
    }

    public function createNewToken(User $user, Request $request)
    {
        $this->rememberMeDAO->deleteTokenByUsername($user->getUsername());
        
        $series = base64_encode(random_bytes(64));
        $tokenValue = base64_encode(random_bytes(64));

        $this->rememberMeDAO->insertToken(
            new PersistentToken(
                \get_class($user),
                $user->getUsername(),
                $series,
                $tokenValue,
                new DateTime()
            )
        );

        $request->attributes->set(
            self::COOKIE_ATTR_NAME,
            new Cookie(
                $this->options['name'],
                $this->encodeCookie([$series, $tokenValue]),
                time() + $this->options['lifetime'],
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'] ?? $request->isSecure(),
                $this->options['httponly'],
                false,
                $this->options['samesite']
            )
        );
    }

    public function deleteToken(Request $request)
    {
        $this->rememberMeDAO->deleteTokenByUsername($this->tokenStorage->getToken()->getUsername());
        $this->cancelCookie($request);
    }

    protected function encodeCookie(array $cookieParts)
    {
        foreach ($cookieParts as $cookiePart) {
            if (false !== strpos($cookiePart, self::COOKIE_DELIMITER)) {
                throw new Exception(
                    sprintf('$cookieParts should not contain the cookie delimiter "%s".', self::COOKIE_DELIMITER)
                );
            }
        }

        return base64_encode(implode(self::COOKIE_DELIMITER, $cookieParts));
    }

    protected function decodeCookie($rawCookie)
    {
        return explode(self::COOKIE_DELIMITER, base64_decode($rawCookie));
    }

    protected function cancelCookie(Request $request)
    {
        $request->attributes->set(
            self::COOKIE_ATTR_NAME,
            new Cookie(
                $this->options['name'],
                null,
                1,
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'] ?? $request->isSecure(),
                $this->options['httponly'],
                false,
                $this->options['samesite']
            )
        );
    }
}
