<?php

namespace Framework\Security\RememberMe;

use DateTime;
use Exception;
use App\DAO\UserDAO;
use Framework\Cookie\Cookie;
use Framework\Request\Request;
use Framework\Security\TokenStorage;
use Framework\Security\User\UserInterface;

class RememberMeManager
{
    public const COOKIE_DELIMITER = ':';
    public const COOKIE_ATTR_NAME = 'remember_me_cookie';
    public const COOKIE_NAME = 'REMEMBERME';

    protected array $options = [
        'name' => 'REMEMBERME',
        'lifetime' => 31536000,
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'samesite' => null,
    ];

    private RememberMeDAO $rememberMeDAO;
    private UserDAO $userDAO;
    private TokenStorage $tokenStorage;

    public function __construct(RememberMeDAO $rememberMeDAO, UserDAO $userDAO, TokenStorage $tokenStorage)
    {
        $this->rememberMeDAO = $rememberMeDAO;
        $this->userDAO = $userDAO;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Processes the auto login with cookie remember me.
     */
    public function processAutoLoginCookie(array $cookieParts, Request $request): ?UserInterface
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
            $this->rememberMeDAO->deleteTokenByUsername($persistentToken->getUsername());
            throw new Exception('This token was already used. The account is possibly compromised.');
        }

        if ((int) $persistentToken->getLastUsed()->getTimestamp() + (int) $this->options['lifetime'] < time()) {
            throw new Exception('The cookie has expired.');
        }

        $tokenValue = base64_encode(random_bytes(64));

        $this->rememberMeDAO->updateToken($series, $tokenValue, new DateTime());

        $request->attributes->set(
            self::COOKIE_ATTR_NAME,
            new Cookie(
                $this->options['name'],
                $this->encodeCookie([$series, $tokenValue]),
                (string) (time() + $this->options['lifetime']),
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'] ?? $request->isSecure(),
                $this->options['httponly'],
                false,
                $this->options['samesite']
            )
        );

        /** @var UserInterface */
        return $this->userDAO->getOneByUsername($persistentToken->getUsername());
    }

    public function autoLogin(Request $request): ?RememberMeToken
    {
        if (($cookie = $request->attributes->get(self::COOKIE_ATTR_NAME)) && null === $cookie->getValue()) {
            return null;
        }

        if (null === $cookie = $request->cookies->get($this->options['name'])) {
            return null;
        }

        $cookieParts = $this->decodeCookie($cookie);
        $user = $this->processAutoLoginCookie($cookieParts, $request);

        if (!$user instanceof UserInterface) {
            throw new Exception('processAutoLoginCookie() must return a User class.');
        }

        return new RememberMeToken($user);
    }

    public function createNewToken(UserInterface $user, Request $request): void
    {
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
                (string) (time() + $this->options['lifetime']),
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'] ?? $request->isSecure(),
                $this->options['httponly'],
                false,
                $this->options['samesite']
            )
        );
    }

    public function deleteToken(Request $request): void
    {
        if (!empty($this->tokenStorage->getToken())) {
            $this->rememberMeDAO->deleteTokenByUsername((string) $this->tokenStorage->getToken()->getUsername());
        }
        $this->cancelCookie($request);
    }

    public function deleteTokenBySeries(Request $request): void
    {
        if (($cookie = $request->attributes->get(self::COOKIE_ATTR_NAME)) && null === $cookie->getValue()) {
            return;
        }

        if (null === $cookie = $request->cookies->get($this->options['name'])) {
            return;
        }

        $cookieParts = $this->decodeCookie($cookie);
        [$series, $tokenValue] = $cookieParts;

        if (!empty($this->tokenStorage->getToken())) {
            $this->rememberMeDAO->deleteTokenBySeries($series);
        }
        $this->cancelCookie($request);
    }

    protected function encodeCookie(array $cookieParts): string
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

    protected function decodeCookie(string $rawCookie): array
    {
        return explode(self::COOKIE_DELIMITER, base64_decode($rawCookie));
    }

    protected function cancelCookie(Request $request): void
    {
        $request->attributes->set(
            self::COOKIE_ATTR_NAME,
            new Cookie(
                $this->options['name'],
                null,
                (string) 1,
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
