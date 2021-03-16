<?php

namespace App\DAO;

use Config\DAO\AbstractDAO;

class DAO extends AbstractDAO
{
    /**
     * @param int|string $value
     */
    public function getCountBy(string $table, string $colName, $value, string $mode = null): int
    {
        if ('LIKE' === strtoupper((string) $mode)) {
            $sqlMode = ' LIKE :';
        } else {
            $sqlMode = ' = :';
        }
        $sql = 'SELECT COUNT(id) AS count FROM ' . $table . ' WHERE ' . $colName . $sqlMode . $colName;
        $count = 0;
        $stmt = $this->createQuery($sql, [$colName => $value]);
        $stmt->bindColumn(1, $count);
        $stmt->fetchAll(\PDO::FETCH_BOUND);
        $stmt->closeCursor();

        return $count;
    }
}
