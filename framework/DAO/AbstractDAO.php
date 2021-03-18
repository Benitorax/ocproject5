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
     * @param mixed[] $parameters = ['id' => $id, 'username' => $username]
     * @return null|User|Comment|Post|PersistentToken
     */
    public function selectOneResultBy(
        DAOInterface $dao,
        string $sqlPrefix,
        array $parameters,
        ?array $orderBy = [],
        ?array $limit = []
    ) {
        $stmt = $this->select($sqlPrefix, $parameters, $orderBy, $limit);
        $result = $stmt->fetchObject(stdClass::class);
        $stmt->closeCursor();

        if (false === $result) {
            return null;
        }

        return $dao->buildObject($result);
    }

    /**
     * @param mixed[] $parameters = ['id' => $id, 'username' => $username]
     * @return null|User[]|Comment[]|Post[]|PersistentToken[]
     */
    public function selectResultBy(
        DAOInterface $dao,
        string $sqlPrefix,
        array $parameters,
        ?array $orderBy = [],
        ?array $limit = []
    ) {
        $stmt = $this->select($sqlPrefix, $parameters, $orderBy, $limit);
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
     * @return null|User[]|Comment[]|Post[]|PersistentToken[]
     */
    public function selectAll(DAOInterface $dao, string $sqlPrefix, ?array $orderBy = null, ?array $limit = null)
    {
        $stmt = $this->select($sqlPrefix, null, $orderBy, $limit);
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
     * @param mixed[] $orderBy = ['updated_at' => DESC, 'created_at' => DESC]
     * @param mixed[] $limit = [$offset, $range] = [10, 10]
     * @return PDOStatement
     */
    public function select(string $sql, ?array $parameters = null, ?array $orderBy = null, ?array $limit = null)
    {
        $sql = $this->addWhere($sql, $parameters);
        $sql = $this->addOrderBy($sql, $orderBy);
        $sql = $this->addLimit($sql, $limit);

        return $this->createQuery($sql, $parameters);
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
        if (empty($orderBy)) {
            return $sql;
        }

        $i = 1;
        $where = ' WHERE ';

        // TO DO replace $parameters with array_keys($parameters) to delete $value
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

    /**
     * @param mixed[] $orderBy = ['updated_at' => DESC, 'created_at' => DESC]
     */
    private function addOrderBy(string $sql, ?array $orderBy): string
    {
        if (empty($orderBy)) {
            return $sql;
        }

        $i = 1;
        $order = ' ORDER BY ';

        foreach ($orderBy as $key => $value) {
            if ($i < count($orderBy)) {
                $order .= $key . ' ' . $value . ', ';
            } else {
                $order .= $key . ' ' . $value;
            }
            $i++;
        }

        return $sql . $order;
    }

    /**
     * @param mixed[] $limit = [$offset, $range] = [10, 10]
     */
    public function addLimit(string $sql, ?array $limit): string
    {
        if (empty($limit)) {
            return $sql;
        }

        return $sql . ' LIMIT ' . $limit[0] . ', ' . $limit[1];
    }
}
