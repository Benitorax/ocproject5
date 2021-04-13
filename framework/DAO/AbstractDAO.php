<?php

namespace Framework\DAO;

use PDO;
use stdClass;
use Exception;
use PDOStatement;
use App\Model\Post;
use App\Model\User;
use App\Model\Comment;
use Framework\DAO\DAOInterface;
use App\Model\ResetPasswordToken;
use Framework\DAO\QueryExpression;
use Framework\Security\RememberMe\PersistentToken;

abstract class AbstractDAO implements DAOInterface
{
    /** @var PDO */
    private $connection;

    private function checkConnection(): PDO
    {
        if (null === $this->connection) {
            return $this->getConnection();
        }

        return $this->connection;
    }

    private function getConnection(): PDO
    {
        try {
            $this->connection = new PDO($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connection;
        } catch (Exception $connectionError) {
            die('Connection error:' . $connectionError->getMessage());
        }
    }

    /** @param mixed[] $parameters for bindValue() method*/
    protected function createQuery(string $sql, array $parameters = null): PDOStatement
    {
        if ($parameters) {
            $stmt = $this->checkConnection()->prepare($sql);

            foreach ($parameters as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute($parameters);

            /** @var PDOStatement */
            return $stmt;
        } else {
            $stmt = $this->checkConnection()->query($sql);

            /** @var PDOStatement */
            return $stmt;
        }
    }

    /**
     * @return null|User|Comment|Post|PersistentToken|ResetPasswordToken
     */
    public function getOneResult(
        DAOInterface $dao,
        QueryExpression $query
    ) {
        $stmt = $this->createQuery($query->generateSQL(), $query->getParameters());
        $result = $stmt->fetchObject(stdClass::class);
        $stmt->closeCursor();

        if (false === $result) {
            return null;
        }

        return $dao->buildObject($result);
    }

    /**
     * @return null|User[]|Comment[]|Post[]|PersistentToken[]
     */
    public function getResult(
        DAOInterface $dao,
        QueryExpression $query
    ) {
        $stmt = $this->createQuery($query->generateSQL(), $query->getParameters());
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, stdClass::class);
        $stmt->closeCursor();

        if (false === $result) {
            return null;
        }

        $objects = [];
        foreach ($result as $row) {
            $objects[] = $dao->buildObject($row);
        }

        return $objects;
    }

    /**
     * @param mixed[] $parameters = ['id' => $id, 'username' => $username]
     */
    public function insert(string $tableName, array $parameters): void
    {
        [$colNameString, $paramString] = $this->paramsToStrings($parameters);

        $sql = 'INSERT INTO ' . $tableName . ' (' . $colNameString . ') VALUES (' . $paramString . ')';
        $stmt = $this->createQuery($sql, $parameters);
        $stmt->closeCursor();
    }

    /**
     * @param mixed[] $parameters = ['id' => $id, 'username' => $username]
     */
    public function delete(string $tableName, array $parameters): void
    {
        $sql = 'DELETE FROM ' . $tableName;
        $sql = $this->addWhere($sql, $parameters);
        $stmt = $this->createQuery($sql, $parameters);
        $stmt->closeCursor();
    }

    /**
     * @param mixed[] $parameters = ['password' => $password, 'username' => $username]
     * @param mixed[] $where = ['id' => $id]
     */
    public function update(string $tableName, array $parameters, array $where): void
    {
        $sql = 'UPDATE ' . $tableName . ' SET';

        $i = 1;
        foreach (array_keys($parameters) as $colName) {
            $sql .= ' ' . $colName . '=:' . $colName;
            if ($i < count($parameters)) {
                $sql .= ', ';
            }
            $i++;
        }

        $sql = $this->addWhere($sql, $where);
        $parameters = array_merge($parameters, $where);

        $stmt = $this->createQuery($sql, $parameters);
        $stmt->closeCursor();
    }

    /**
     * @param mixed[] $parameters = ['title' => $title, 'short_text' => $shortText]
     * @return string[]
     */
    private function paramsToStrings(array $parameters): array
    {
        $i = 1;
        $colNameString = '';
        $paramString = '';

        // TO DO replace $parameters with array_keys($parameters) to delete $value
        foreach (array_keys($parameters) as $key) {
            if ($i < count($parameters)) {
                $colNameString .= $key . ', ';
                $paramString .= ':' . $key . ', ';
            } else {
                $colNameString .= $key;
                $paramString .= ':' . $key;
            }
            $i++;
        }

        return [$colNameString, $paramString];
    }

    /**
     * @param mixed[] $parameters = ['id' => $id, 'username' => $username]
     */
    private function addWhere(string $sql, ?array $parameters): string
    {
        if (empty($parameters)) {
            return $sql;
        }

        $i = 1;
        $where = ' WHERE ';

        foreach (array_keys((array) $parameters) as $key) {
            if ($i < count((array) $parameters)) {
                $where .= $key . ' = :' . $key . ' AND ';
            } else {
                $where .= $key . ' = :' . $key;
            }
            $i++;
        }

        return $sql . $where;
    }
}
