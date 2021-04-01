<?php

namespace App\DAO;

use DateTime;
use App\Model\User;
use Ramsey\Uuid\Uuid;
use Framework\DAO\AbstractDAO;
use Framework\DAO\QueryExpression;
use App\Service\Pagination\PaginationDAOInterface;

class UserDAO extends AbstractDAO implements PaginationDAOInterface
{
    private QueryExpression $query;

    /**
     * Returns an User object from stdClass.
     */
    public function buildObject(\stdClass $o): User
    {
        $user = new User();
        $user->setId($o->u_id)
            ->setUuid(Uuid::fromString($o->u_uuid))
            ->setEmail($o->u_email)
            ->setPassword($o->u_password)
            ->setUsername($o->u_username)
            ->setCreatedAt(new DateTime($o->u_created_at))
            ->setUpdatedAt(new DateTime($o->u_updated_at))
            ->setRoles(json_decode($o->u_roles))
            ->setIsBlocked($o->u_is_blocked);

        return $user;
    }

    /**
     * Setting the query without executing it.
     */
    public function setAllUsersQuery(?string $filter, ?string $search): void
    {
        $this->prepareQuery();

        if (null !== $search && '' !== $search) {
            $this->query->addWhere(
                'username LIKE :search'
                    . ' OR email LIKE :search'
            )
            ->setParameter('search', '%' . $search . '%');
        }

        if ($filter === 'blocked') {
            $this->query->addWhere('is_blocked = 1');
        } elseif ($filter === 'unblocked') {
            $this->query->addWhere('is_blocked = 0');
        }
    }

    /**
     * @return null|object|User the object is instance of User class
     */
    public function getOneByUsername(string $username)
    {
        $this->prepareQuery()
            ->where('username = :username')
            ->setParameter('username', $username);

        return $this->getOneResult($this, $this->query);
    }

    /**
     * @return null|object|User the object is instance of User class
     */
    public function getOneByEmail(string $email)
    {
        $this->prepareQuery()
            ->where('email = :email')
            ->setParameter('email', $email);

        return $this->getOneResult($this, $this->query);
    }

    /**
     * @return null|object[]|User[] Array of admin users
     */
    public function getAllAdmin()
    {
        $this->prepareQuery()
            ->where('roles LIKE \'%admin%\'');

        return $this->getResult($this, $this->query);
    }

    /**
     * Sets the select and the table for the sql query.
     */
    private function prepareQuery(): QueryExpression
    {
        return $this->query = (new QueryExpression())
            ->select(User::SQL_COLUMNS, 'u')
            ->from(User::SQL_TABLE, 'u')
            ->orderBy('created_at', 'DESC');
    }

    /**
     * Inserts a new row in the database.
     */
    public function add(User $user): void
    {
        $this->insert('user', [
            'uuid' => $user->getUuid()->toString(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'username' => $user->getUsername(),
            'created_at' => ($user->getCreatedAt())->format('Y-m-d H:i:s'),
            'updated_at' => ($user->getUpdatedAt())->format('Y-m-d H:i:s'),
            'roles' => json_encode($user->getRoles()),
            'is_blocked' => intval($user->getIsBlocked())
        ]);
    }

    /**
     * Update a blocked user.
     */
    public function blockByUuid(string $uuid): void
    {
        $this->update('user', ['is_blocked' => 1], ['uuid' => $uuid]);
    }

    /**
     * Update a unblocked user.
     */
    public function unblockByUuid(string $uuid): void
    {
        $this->update('user', ['is_blocked' => 0], ['uuid' => $uuid]);
    }

    /**
     * Returns the total count of posts.
     */
    public function getPaginationCount(): int
    {
        $stmt = $this->createQuery($this->query->generateCountSQL(), $this->query->getParameters());
        $result = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int) $result;
    }

    /**
     * @return null|object[]|User[] Array of posts
     */
    public function getPaginationResult(int $offset, int $range)
    {
        $this->query->limit($offset, $range);

        return $this->getResult($this, $this->query);
    }
}
