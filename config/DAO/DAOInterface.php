<?php

namespace Config\DAO;

use stdClass;
use App\Model\Post;
use App\Model\User;
use App\Model\Comment;
use Config\Security\RememberMe\PersistentToken;

/**
 * Interface of DAO classes.
 */
interface DAOInterface
{
    /**
     * @param stdClass $class from database with PDOStatement fetch
     * @return User|Comment|Post|PersistentToken A model object
     */
    public function buildObject(stdClass $class);
}
