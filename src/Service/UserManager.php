<?php

namespace App\Service;

use DateTime;
use App\Model\User;
use App\DAO\UserDAO;
use Ramsey\Uuid\Uuid;
use App\Service\Pagination\Paginator;
use Framework\Security\Encoder\PasswordEncoder;

class UserManager
{
    private UserDAO $userDAO;
    private PasswordEncoder $encoder;
    private Paginator $paginator;

    public function __construct(
        UserDAO $userDAO,
        PasswordEncoder $encoder,
        Paginator $paginator
    ) {
        $this->userDAO = $userDAO;
        $this->encoder = $encoder;
        $this->paginator = $paginator;
    }

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
     * Blocks user by its id in database.
     */
    public function blockUserByUuid(string $uuid): void
    {
        $this->userDAO->blockByUuid($uuid);
    }

    /**
     * Unblocks user by its id in database.
     */
    public function unblockUserByUuid(string $uuid): void
    {
        $this->userDAO->unblockByUuid($uuid);
    }
}
