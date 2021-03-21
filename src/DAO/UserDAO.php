<?php

namespace App\DAO;

use DateTime;
use App\Model\User;
use Framework\DAO\AbstractDAO;
use Framework\DAO\QueryExpression;

class UserDAO extends AbstractDAO
{
    private QueryExpression $query;

    public function __construct()
    {
        $this->query = new QueryExpression();
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

    private function prepareQuery(): QueryExpression
    {
        return $this->query->select(User::SQL_COLUMNS, 'u')
            ->from(User::SQL_TABLE, 'u');
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
