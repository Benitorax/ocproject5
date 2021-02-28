<?php
namespace App\DAO;

use Config\DAO\AbstractDAO;

class DAO extends AbstractDAO
{
    public function getCountBy(string $table, string $colName, $value): int
    {
        $sql = 'SELECT COUNT(*) AS count FROM '.$table.' WHERE '.$colName.' = :'.$colName;
        $result = $this->createQuery($sql, [$colName => $value]);
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        $result->closeCursor();

        return $row['count'];
    }
}