<?php

namespace App\DAO;

use DateTime;
use App\Model\User;
use Framework\DAO\AbstractDAO;
use Framework\DAO\DAOInterface;

class UserDAO extends AbstractDAO implements DAOInterface
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
        return $this->selectOneResultBy(self::SQL_SELECT, $parameters, $this);
    }

    /**
     * @return null|object[]|User[] Array of users
     */
    public function getBy(array $parameters)
    {
        return $this->selectResultBy(self::SQL_SELECT, $parameters, $this);
    }

    /**
     * @return null|object[]|User[] Array of all users
     */
    public function getAll()
    {
        return $this->selectAll(self::SQL_SELECT, $this);
    }

    /**
     * @return null|object[]|User[] Array of users who have role admin
     */
    public function getAllAdmin()
    {
        return $this->selectAll(self::SQL_SELECT . ' WHERE roles LIKE \'%admin%\'', $this);
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
}
