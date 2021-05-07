<?php

namespace Framework\DAO;

use stdClass;

/**
 * Must implement this interface if extends AbstractDAO.
 */
interface DAOInterface
{
    /**
     * Returns a model object from stdClass provided by the PDOStatement.
     *
     * @param stdClass $class from database with PDOStatement fetch
     * @return mixed|object A model object
     */
    public function buildObject(stdClass $class);
}
