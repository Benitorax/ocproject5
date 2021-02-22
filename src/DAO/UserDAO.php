<?php
namespace App\DAO;

use App\Model\User;
use Config\DAO\AbstractDAO;

class UserDAO extends AbstractDAO
{
    private function buildObject($row): User
    {
        $user = new User();
        $user->setId($row['id'])
            ->setEmail($row['email'])
            ->setPassword($row['password'])
            ->setUsername($row['username'])
            ->setCreatedAt($row['created_at'])
            ->setUpdatedAt($row['updated_at'])
            ->setIsAdmin($row['is_admin'])
            ->setIsBlocked($row['is_blocked']);

        return $user;
    }

    public function get()
    {
        $sql = 'SELECT id, email, password, username, created_at, updated_at, is_admin, is_blocked FROM user ORDER BY id DESC';
        $result = $this->createQuery($sql);
        $users = [];
        foreach ($result as $row){
            $userId = $row['id'];
            $users[$userId] = $this->buildObject($row);
        }
        $result->closeCursor();

        return $users;
    }

    public function getById($userId): User
    {
        $sql = 'SELECT id, email, password, username, created_at, updated_at, is_admin, is_blocked FROM user ORDER BY id DESC';
        $result = $this->createQuery($sql, [$userId]);
        $user = $result->fetch();
        $result->closeCursor();

        return $this->buildObject($user);
    }

    public function add(User $user)
    {
        $sql = 'INSERT INTO user (id, email, password, username, created_at, updated_at, is_admin, is_blocked) 
            VALUES (:id, :email, :password, :username, :created_at, :updated_at, :is_admin, :is_blocked)';
        $this->createQuery($sql, [
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