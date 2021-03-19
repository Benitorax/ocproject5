<?php

namespace App\DAO;

use DateTime;
use App\Model\User;
use App\Service\Pagination\PaginationDAOInterface;
use Framework\DAO\AbstractDAO;

class UserDAO extends AbstractDAO implements PaginationDAOInterface
{
    private string $sqlSelect;

    public function __construct(SQLGenerator $sqlGenerator)
    {
        $this->sqlSelect =  'SELECT ' . $sqlGenerator->generateStringWithAlias('u', User::SQL_COLUMNS)
                            . ' From User u';
    }

    public function buildObject(\stdClass $o): User
    {
        $user = new User();
        $user->setId($o->u_id)
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
     * @return null|object|User the object is instance of User class
     */
    public function getOneBy(array $parameters)
    {
        return $this->selectOneResultBy($this, $this->sqlSelect, $parameters);
    }

    /**
     * @return null|object[]|User[] Array of users
     */
    public function getBy(array $parameters, array $orderBy = [], array $limit = [])
    {
        return $this->selectResultBy($this, $this->sqlSelect, $parameters);
    }

    /**
     * @return null|object[]|User[] Array of all users
     */
    public function getAll()
    {
        return $this->selectAll($this, $this->sqlSelect);
    }

    /**
     * @return null|object[]|User[] Array of users who have role admin
     */
    public function getAllAdmin()
    {
        return $this->selectAll($this, $this->sqlSelect . ' WHERE roles LIKE \'%admin%\'');
    }

    public function add(User $user): void
    {
        $this->insert('user', [
            'id' => $user->getId(),
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
     * Returns the total count of users.
     */
    public function getCountBy(array $parameters): int
    {
        $sql = 'SELECT COUNT(*) FROM user';
        $stmt = $this->createQuery($sql, $parameters);
        $result = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int) $result;
    }
}
