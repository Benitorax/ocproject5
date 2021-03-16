<?php

namespace Config\DAO;

use stdClass;
use App\Model\Post;
use App\Model\User;
use App\Model\Comment;
use Config\Security\RememberMe\PersistentToken;

/**
 * Must implement this interface if extends AbstractDAO.
 */
interface DAOInterface
{
    /**
     * Returns a model object from stdClass provided by the PDOStatement.
     *
     * @param stdClass $class from database with PDOStatement fetch
     * @return User|Comment|Post|PersistentToken A model object
     */
    public function buildObject(stdClass $class);
}
