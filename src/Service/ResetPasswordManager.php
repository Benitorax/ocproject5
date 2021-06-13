<?php

namespace App\Service;

use Exception;
use App\Model\User;
use DateTimeImmutable;
use DateTimeInterface;
use Framework\Session\Session;
use App\Model\ResetPasswordToken;
use App\DAO\ResetPasswordTokenDAO;
use App\Service\Mailer\Notification;

/**
 * Some of the cryptographic strategies were taken from SymfonyCasts/reset-password-bundle and
 * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
 */
class ResetPasswordManager
{
    /**
     * The first 20 characters of the token are a "selector".
     */
    private const SELECTOR_LENGTH = 20;

    private const SIGNING_KEY = 'reset_password';
    private const INVALID_TOKEN_MESSAGE = 'The reset password link is invalid.';
    private const EXPIRED_TOKEN_MESSAGE = 'The link in your email is expired.';

    /**
     * How long a token is valid in seconds
     */
    private int $resetRequestLifetime = 60 * 60;

    private UserManager $userManager;
    private ResetPasswordTokenDAO $tokenDAO;
    private Notification $notification;
    private Session $session;

    public function __construct(
        UserManager $userManager,
        ResetPasswordTokenDAO $tokenDAO,
        Notification $notification,
        Session $session
    ) {
        $this->userManager = $userManager;
        $this->tokenDAO = $tokenDAO;
        $this->notification = $notification;
        $this->session = $session;
    }

    /**
     * Generates a token and sends an email with a url to reset password.
     */
    public function manageResetRequest(string $email): void
    {
        $this->addFlash(
            'success',
            sprintf(
                'If you\'re registered with %s, then an email has been sent to reset your password.',
                $email
            )
        );

        $this->tokenDAO->deleteExpiredTokens();
        $user = $this->userManager->getOneByEmail($email);

        if (!$user instanceof User) {
            return;
        }

        $token = $this->generateToken($user);
        $this->notification->notifyResetPasswordRequest($user, $token);
    }

    /**
     * Deletes ResetPasswordToken from database and updates password of the user.
     */
    public function manageReset(User $user, string $password): void
    {
        $this->tokenDAO->deleteByUserId($user->getId());
        $this->userManager->updatePasswordToUser($user, $password);
        $this->addFlash('success', 'The password has been reset with success!');
    }

    /**
     * Generates a ResetPasswordToken and persists it in database.
     */
    public function generateToken(User $user): ResetPasswordToken
    {
        $expiredAt = new DateTimeImmutable(sprintf('+%d seconds', $this->resetRequestLifetime));
        $selector = $this->getRandomAlphaNumStr();
        $verifier = $this->getRandomAlphaNumStr();
        $hashedToken = $this->getHashedToken($expiredAt, $user->getId(), $verifier);

        $token = new ResetPasswordToken($user, $expiredAt, $selector, $hashedToken);
        $token->setVerifier($verifier);

        $this->tokenDAO->ensureOneTokenInDatabase($token);

        return $token;
    }

    /**
     * Validates token and fetchs user from token.
     */
    public function validateTokenAndFetchUser(string $token): User
    {
        if (40 !== \strlen($token)) {
            throw $this->addFlashAndReturnException(self::INVALID_TOKEN_MESSAGE);
        }

        $resetToken = $this->getResetPasswordToken($token);

        if (null === $resetToken) {
            throw $this->addFlashAndReturnException(self::INVALID_TOKEN_MESSAGE);
        }

        if ($resetToken->isExpired()) {
            throw $this->addFlashAndReturnException(self::EXPIRED_TOKEN_MESSAGE);
        }

        $user = $resetToken->getUser();

        $hashedToken = $this->getHashedToken(
            $resetToken->getExpiredAt(),
            $user->getId(),
            substr($token, self::SELECTOR_LENGTH)
        );

        if (false === hash_equals($resetToken->getHashedToken(), $hashedToken)) {
            throw $this->addFlashAndReturnException(self::INVALID_TOKEN_MESSAGE);
        }

        return $user;
    }

    /**
     * Adds flash message and returns an Exception with the message.
     */
    public function addFlashAndReturnException(string $message): Exception
    {
        $this->addFlash('danger', $message . ' Please try to reset your password again.');

        return new Exception($message);
    }


    /**
     * Adds flash message.
     */
    public function addFlash(string $type, string $message): void
    {
        $this->session->getFlashes()->add($type, $message);
    }

    /**
     * Original credit to Laravel's Str::random() method.
     *
     * String length is 20 characters
     */
    private function getRandomAlphaNumStr(): string
    {
        $string = '';

        while (($len = strlen($string)) < 20) {
            $size = 20 - $len;

            $bytes = random_bytes($size);

            $string .= substr(
                str_replace(['/', '+', '='], '', base64_encode($bytes)),
                0,
                $size
            );
        }

        return $string;
    }

    /**
     * Returns a hashed token.
     */
    private function getHashedToken(DateTimeInterface $expiredAt, int $userId, string $verifier = null): string
    {
        $encodedData = json_encode([$verifier, $userId, $expiredAt->getTimestamp()]);

        return base64_encode(hash_hmac('sha256', (string) $encodedData, self::SIGNING_KEY, true));
    }

    /**
     * Returns a ResetPasswordToken or null.
     */
    private function getResetPasswordToken(string $token): ?ResetPasswordToken
    {
        $selector = substr($token, 0, self::SELECTOR_LENGTH);

        return $this->tokenDAO->getOneBySelector($selector);
    }
}
