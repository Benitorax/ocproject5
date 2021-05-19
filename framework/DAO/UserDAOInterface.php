<?php

namespace Framework\DAO;

use Framework\Security\User\UserInterface;

interface UserDAOInterface
{
    /**
     * Returns an User class which implements UserInterface
     *
     * @return UserInterface
     */
    public function getOneByUsername(string $username);
}
