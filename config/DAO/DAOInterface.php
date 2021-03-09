<?php
namespace Config\DAO;

/**
 * Interface of DAO classes.
 */
interface DAOInterface
{
    /**
     * @param stdClass $class from database with PDOStatement fetch
     * @return <object> A model object
     */
    public function buildObject(\stdClass $class);
}
