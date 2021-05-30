<?php

namespace Framework\DAO;

use PDO;
use stdClass;
use PDOStatement;
use Framework\DAO\Connection;
use Framework\DAO\DAOInterface;
use Framework\DAO\QueryExpression;

abstract class AbstractDAO implements DAOInterface
{
    private Connection $connection;

    protected function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /** @param mixed[] $parameters for bindValue() method*/
    protected function createQuery(string $sql, array $parameters = null): PDOStatement
    {
        if ($parameters) {
            /** @var PDOStatement */
            $stmt = $this->connection->prepare($sql);

            foreach ($parameters as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute($parameters);

            return $stmt;
        }

        $stmt = $this->connection->query($sql);

        /** @var PDOStatement */
        return $stmt;
    }

    /**
     * Returns an instance of the class returned by buildObject method.
     *
     * @return null|object
     */
    public function getOneResult(DAOInterface $dao, QueryExpression $query)
    {
        $stmt = $this->createQuery($query->generateSQL(), $query->getParameters());
        $result = $stmt->fetchObject(stdClass::class);
        $stmt->closeCursor();

        if (false === $result) {
            return null;
        }

        return $dao->buildObject($result);
    }

    /**
     * @return null|object[]
     */
    public function getResult(DAOInterface $dao, QueryExpression $query)
    {
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
        $sql = 'UPDATE ' . $tableName;

        $sql .= ' SET ' . implode(', ', array_map(function ($colName) {
            return $colName . '=:' . $colName;
        }, array_keys($parameters)));

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
        $keys = array_keys($parameters);
        $colNameString = implode(', ', $keys);
        $paramString = implode(', ', array_map(function ($element) {
            return ':' . $element;
        }, $keys));

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

        $where = ' WHERE ' . implode(' AND ', array_map(function ($element) {
            return $element . ' = :' . $element;
        }, array_keys($parameters)));

        return $sql . $where;
    }
}
