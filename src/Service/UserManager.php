<?php

namespace App\Service;

use DateTime;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use Ramsey\Uuid\Uuid;
use App\Service\Mailer\Notification;
use App\Service\Pagination\Paginator;
use Framework\Security\Encoder\PasswordEncoder;

class UserManager
{
    private UserDAO $userDAO;
    private PostDAO $postDAO;
    private PasswordEncoder $encoder;
    private Paginator $paginator;
    private Notification $notification;

    public function __construct(
        UserDAO $userDAO,
        PostDAO $postDAO,
        PasswordEncoder $encoder,
        Paginator $paginator,
        Notification $notification
    ) {
        $this->userDAO = $userDAO;
        $this->postDAO = $postDAO;
        $this->encoder = $encoder;
        $this->paginator = $paginator;
        $this->notification = $notification;
    }

    /**
     * Returns one User by email.
     */
    public function getOneByEmail(string $email): ?User
    {
        return $this->userDAO->getOneByEmail($email);
    }

    /**
     * Saves a User in database and returns it.
     */
    public function saveNewUser(User $user): User
    {
        $dateTime = new DateTime();
        $user->setUuid(Uuid::uuid4())
            ->setPassword((string) $this->encoder->encode($user->getPassword()))
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
        ;

        $this->userDAO->add($user);

        return $user;
    }

    /**
     * Saves the new encoded password in database and notify the user.
     */
    public function updatePasswordToUser(User $user, string $password): void
    {
        $user->setPassword((string) $this->encoder->encode($password))
            ->setUpdatedAt(new DateTime())
        ;

        $this->userDAO->updateUser($user);
        $this->notification->notifyResetPassword($user);
    }

    /**
     * Returns a Paginator.
     */
    public function getPaginationForAllUsers(?string $filter, ?string $searchTerms = null, ?int $pageNumber): Paginator
    {
        // sets the query for the pagination
        $this->userDAO->setAllUsersQuery($filter, $searchTerms);

        // creates the pagination for the template
        return $this->paginator->paginate(
            $this->userDAO,
            $pageNumber ?? 1,
            15
        );
    }

    /**
     * Blocks user in database by id.
     */
    public function blockUserByUuid(string $uuid): void
    {
        $this->userDAO->blockByUuid($uuid);
    }

    /**
     * Unblocks user in database by id.
     */
    public function unblockUserByUuid(string $uuid): void
    {
        $this->userDAO->unblockByUuid($uuid);
    }

    /**
     * Deletes user in database by id.
     */
    public function deleteUserByUuid(string $uuid): void
    {
        $user = $this->userDAO->getOneByUuid($uuid);

        if (!$user instanceof User) {
            return;
        }

        if (in_array('admin', $user->getRoles())) {
            $this->postDAO->setAuthorToNull($user);
        }

        $this->userDAO->deleteUser($user);
    }
}
