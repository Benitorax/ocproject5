<?php

namespace Framework\DAO;

use PDO;
use Framework\DAO\AbstractDAO;
use stdClass;

class DAO extends AbstractDAO
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    public function buildObject(stdClass $class): stdClass
    {
        return $class;
    }

    /**
     * returns the count of a SQL command.
     *
     * @param int|string $value
     */
    public function getCountBy(string $table, string $colName, $value, string $mode = null): int
    {
        $sqlMode = ' = :';

        if ('LIKE' === strtoupper((string) $mode)) {
            $sqlMode = ' LIKE :';
        }

        $sql = 'SELECT COUNT(id) AS count FROM ' . $table . ' WHERE ' . $colName . $sqlMode . $colName;
        $count = 0;
        $stmt = $this->createQuery($sql, [$colName => $value]);
        $stmt->bindColumn(1, $count);
        $stmt->fetchAll(PDO::FETCH_BOUND);
        $stmt->closeCursor();

        return $count;
    }
}
