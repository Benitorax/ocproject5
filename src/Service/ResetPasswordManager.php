<?php

namespace App\Service;

use App\Model\User;
use App\DAO\UserDAO;
use DateTimeImmutable;
use DateTimeInterface;
use Framework\Session\Session;
use App\Model\ResetPasswordToken;
use App\DAO\ResetPasswordTokenDAO;
use App\Service\Mailer\Notification;

class ResetPasswordManager
{
    /**
     * The first 20 characters of the token are a "selector".
     */
    private const SELECTOR_LENGTH = 20;

    private const SIGNING_KEY = 'reset_password';

    /**
     * How long a token is valid in seconds
     */
    private int $resetRequestLifetime = 60 * 60;

    private UserDAO $userDAO;
    private ResetPasswordTokenDAO $resetPasswordTokenDAO;
    private Notification $notification;

    public function __construct(
        UserDAO $userDAO,
        ResetPasswordTokenDAO $resetPasswordTokenDAO,
        Notification $notification,
        Session $session
    ) {
        $this->userDAO = $userDAO;
        $this->resetPasswordTokenDAO = $resetPasswordTokenDAO;
        $this->notification = $notification;
        $this->session = $session;
    }

    /**
     * Generates a token and sends an email with a url to reset password.
     */
    public function manage(string $email): void
    {
        $user = $this->userDAO->getOneByEmail($email);

        if (!$user instanceof User) {
            return;
        }

        $token = $this->generateToken($user);
        $this->notification->notifyResetPasswordRequest($user, $token);
        $this->session->getFlashes()->add(
            'info',
            sprintf('An email has been sent to %s to reset your password.', $email)
        );
    }

    /**
     * Generates a ResetPasswordToken and persists it in database.
     */
    public function generateToken(User $user): ResetPasswordToken
    {
        $expiredAt = new DateTimeImmutable(\sprintf('+%d seconds', $this->resetRequestLifetime));
        $selector = $this->getRandomAlphaNumStr();
        $verifier = $this->getRandomAlphaNumStr();
        $hashedToken = $this->getHashedToken($expiredAt, $user->getId(), $verifier);

        $token = new ResetPasswordToken($user, $expiredAt, $selector, $hashedToken);
        $token->setVerifier($verifier);

        $this->resetPasswordTokenDAO->ensureOneTokenInDatabase($token);

        return $token;
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
}
