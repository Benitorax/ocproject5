<?php
namespace App\DAO;

use App\Model\User;
use Config\DAO\AbstractDAO;
use Config\DAO\DAOInterface;

class UserDAO extends AbstractDAO implements DAOInterface
{
    const SQL_SELECT = 'SELECT id, email, password, username, created_at, updated_at, is_admin, is_blocked FROM user';

    public function buildObject(\stdClass $object): User
    {
        $user = new User();
        $user->setId($object->id)
            ->setEmail($object->email)
            ->setPassword($object->password)
            ->setUsername($object->username)
            ->setCreatedAt(new \DateTime($object->created_at))
            ->setUpdatedAt(new \DateTime($object->updated_at))
            ->setIsAdmin($object->is_admin)
            ->setIsBlocked($object->is_blocked);

        return $user;
    }

    public function getOneBy(array $parameters): User
    {
        return $this->selectOneResultBy(self::SQL_SELECT, $parameters, $this);
    }

    public function getBy(array $parameters): array
    {
        return $this->selectResultBy(self::SQL_SELECT, $parameters, $this);
    }

    public function getAll(): array
    {
        return $this->selectAll(self::SQL_SELECT, $this);
    }

    public function add(User $user)
    {
        $this->insert('user', [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'username' => $user->getUsername(),
            'created_at' => ($user->getCreatedAt())->format('Y-m-d H:i:s'),
            'updated_at' => ($user->getUpdatedAt())->format('Y-m-d H:i:s'),
            'is_admin' => intval($user->getIsAdmin()),
            'is_blocked' => intval($user->getIsBlocked())
        ]);
    }
}