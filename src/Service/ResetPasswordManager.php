<?php

namespace App\Service;

use App\Model\User;
use App\DAO\UserDAO;
use DateTimeImmutable;
use DateTimeInterface;
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
        Notification $notification
    ) {
        $this->userDAO = $userDAO;
        $this->resetPasswordTokenDAO = $resetPasswordTokenDAO;
        $this->notification = $notification;
    }

    public function manage(string $email): void
    {
        $user = $this->userDAO->getOneByEmail($email);

        if (!$user instanceof User) {
            return;
        }

        $this->resetPasswordTokenDAO->deleteByUserId($user->getId());

        $resetPasswordToken = $this->generateResetPasswordToken($user);
        $this->resetPasswordTokenDAO->add($resetPasswordToken);
        $this->notification->notifyResetPasswordRequest($user, $resetPasswordToken);
    }

    public function generateResetPasswordToken(User $user): ResetPasswordToken
    {
        $expiredAt = new DateTimeImmutable(\sprintf('+%d seconds', $this->resetRequestLifetime));
        $selector = $this->getRandomAlphaNumStr();
        $verifier = $this->getRandomAlphaNumStr();
        $hashedToken = $this->getHashedToken($expiredAt, $user->getId(), $verifier);

        $resetPasswordToken = new ResetPasswordToken($user, $expiredAt, $selector, $hashedToken);
        $resetPasswordToken->setVerifier($verifier);

        return $resetPasswordToken;
    }

    /**
     * Original credit to Laravel's Str::random() method.
     *
     * String length is 20 characters
     */
    public function getRandomAlphaNumStr(): string
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

    private function getHashedToken(DateTimeInterface $expiredAt, int $userId, string $verifier = null): string
    {
        $encodedData = json_encode([$verifier, $userId, $expiredAt->getTimestamp()]);

        return base64_encode(hash_hmac('sha256', (string) $encodedData, self::SIGNING_KEY, true));
    }
}
