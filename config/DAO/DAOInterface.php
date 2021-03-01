<?php
namespace Config\DAO;

/**
 * Interface of DAO classes.
 */
interface DAOInterface
{
    /**
     * return <object> A model object from database with PDOStatement fetch.
     */
    public function buildObject(\stdClass $class);
}
