<?php

namespace App\Service;

use Exception;
use App\Model\User;
use App\DAO\UserDAO;
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

    /**
     * How long a token is valid in seconds
     */
    private int $resetRequestLifetime = 60 * 60;

    private UserManager $userManager;
    private ResetPasswordTokenDAO $resetPasswordTokenDAO;
    private Notification $notification;
    private Session $session;

    public function __construct(
        UserManager $userManager,
        ResetPasswordTokenDAO $resetPasswordTokenDAO,
        Notification $notification,
        Session $session
    ) {
        $this->userManager = $userManager;
        $this->resetPasswordTokenDAO = $resetPasswordTokenDAO;
        $this->notification = $notification;
        $this->session = $session;
    }

    /**
     * Generates a token and sends an email with a url to reset password.
     */
    public function manageResetRequest(string $email): void
    {
        $this->resetPasswordTokenDAO->deleteExpiredTokens();
        $user = $this->userManager->getOneByEmail($email);

        if (!$user instanceof User) {
            return;
        }

        $token = $this->generateToken($user);
        $this->notification->notifyResetPasswordRequest($user, $token);
        $this->session->getFlashes()->add(
            'success',
            sprintf('An email has been sent to %s to reset your password.', $email)
        );
    }

    /**
     * Deletes ResetPasswordToken from database and updates password of the user.
     */
    public function manageReset(User $user, string $password): void
    {
        $this->resetPasswordTokenDAO->deleteByUserId($user->getId());
        $this->userManager->updatePasswordToUser($user, $password);
        $this->session->getFlashes()->add('success', 'The password has been reset with success!');
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

        $this->resetPasswordTokenDAO->ensureOneTokenInDatabase($token);

        return $token;
    }

    /**
     * Validates token and fetchs user from token.
     */
    public function validateTokenAndFetchUser(string $fullToken): User
    {
        if (40 !== \strlen($fullToken)) {
            throw new Exception('The reset password link is invalid.');
        }

        $resetToken = $this->getResetPasswordToken($fullToken);

        if (null === $resetToken) {
            throw new Exception('The reset password link is invalid.');
        }

        if ($resetToken->isExpired()) {
            throw new Exception('The link in your email is expired.');
        }

        $user = $resetToken->getUser();

        $hashedTokenFromVerifier = $this->getHashedToken(
            $resetToken->getExpiredAt(),
            $user->getId(),
            substr($fullToken, self::SELECTOR_LENGTH)
        );

        if (false === hash_equals($resetToken->getHashedToken(), $hashedTokenFromVerifier)) {
            throw new Exception('The reset password link is invalid.');
        }

        return $user;
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

        return $this->resetPasswordTokenDAO->getOneBySelector($selector);
    }
}
