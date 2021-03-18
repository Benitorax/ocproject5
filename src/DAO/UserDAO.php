<?php

namespace App\DAO;

use DateTime;
use App\Model\User;
use App\Service\Pagination\PaginationDAOInterface;
use Framework\DAO\AbstractDAO;

class UserDAO extends AbstractDAO implements PaginationDAOInterface
{
    private const SQL_SELECT = 'SELECT id, email, password, username, created_at,'
                                . ' updated_at, roles, is_blocked FROM user';

    public function buildObject(\stdClass $object): User
    {
        $user = new User();
        $user->setId($object->id)
            ->setEmail($object->email)
            ->setPassword($object->password)
            ->setUsername($object->username)
            ->setCreatedAt(new DateTime($object->created_at))
            ->setUpdatedAt(new DateTime($object->updated_at))
            ->setRoles(json_decode($object->roles))
            ->setIsBlocked($object->is_blocked);

        return $user;
    }

    /**
     * @return null|object|User the object is instance of User class
     */
    public function getOneBy(array $parameters)
    {
        return $this->selectOneResultBy($this, self::SQL_SELECT, $parameters);
    }

    /**
     * @return null|object[]|User[] Array of users
     */
    public function getBy(array $parameters, array $orderBy = [], array $limit = [])
    {
        return $this->selectResultBy($this, self::SQL_SELECT, $parameters);
    }

    /**
     * @return null|object[]|User[] Array of all users
     */
    public function getAll()
    {
        return $this->selectAll($this, self::SQL_SELECT);
    }

    /**
     * @return null|object[]|User[] Array of users who have role admin
     */
    public function getAllAdmin()
    {
        return $this->selectAll($this, self::SQL_SELECT . ' WHERE roles LIKE \'%admin%\'');
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
