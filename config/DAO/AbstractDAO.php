<?php

namespace Config\DAO;

use PDO;
use stdClass;
use Exception;
use PDOStatement;
use App\Model\Post;
use App\Model\User;
use App\Model\Comment;
use Config\Security\RememberMe\PersistentToken;

abstract class AbstractDAO
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

    /** @param mixed[] $parameters */
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
    public function selectOneResultBy(string $sqlPrefix, array $parameters, DAOInterface $dao)
    {
        $stmt = $this->select($sqlPrefix, $parameters);
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
    public function selectResultBy(string $sqlPrefix, array $parameters, DAOInterface $dao)
    {
        $stmt = $this->select($sqlPrefix, $parameters);
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
    public function selectAll(string $sqlPrefix, DAOInterface $dao)
    {
        $stmt = $this->select($sqlPrefix);
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
     * @return PDOStatement
     */
    public function select(string $sql, array $parameters = null, array $orderBy = null)
    {
        if ($parameters) {
            $sql = $this->addWhere($sql, $parameters);
        }
        $sql = $this->addOrderBy($sql, $orderBy);

        return $this->createQuery($sql, $parameters);
    }

    /**
     * @param mixed[] $parameters = ['id' => $id, 'username' => $username]
     */
    public function insert(string $tableName, array $parameters): PDOStatement
    {
        [$colNameString, $paramString] = $this->paramsToStrings($parameters);

        $sql = 'INSERT INTO ' . $tableName . ' (' . $colNameString . ') VALUES (' . $paramString . ')';
        return $this->createQuery($sql, $parameters);
    }

    /**
     * @param mixed[] $parameters = ['id' => $id, 'username' => $username]
     */
    public function delete(string $tableName, array $parameters): PDOStatement
    {
        $sql = 'DELETE FROM ' . $tableName;
        $sql = $this->addWhere($sql, $parameters);
        return $this->createQuery($sql, $parameters);
    }

    /**
     * @param mixed[] $parameters = ['password' => $password, 'username' => $username]
     * @param mixed[] $where = ['id' => $id]
     */
    public function update(string $tableName, array $parameters, array $where): PDOStatement
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

        return $this->createQuery($sql, $parameters);
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
        foreach ($parameters as $key => $value) {
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
    private function addWhere(string $sqlPrefix, array $parameters): string
    {
        $i = 1;
        $where = ' WHERE ';

        // TO DO replace $parameters with array_keys($parameters) to delete $value
        foreach (array_keys($parameters) as $key) {
            if ($i < count($parameters)) {
                $where .= $key . ' = :' . $key . ' AND ';
            } else {
                $where .= $key . ' = :' . $key;
            }
            $i++;
        }

        return $sqlPrefix . $where;
    }

    /**
     * @param mixed[] $orderBy = ['updated_at' => DESC, 'created_at' => DESC]
     */
    private function addOrderBy(string $sql, array $orderBy = null): string
    {
        if ($orderBy) {
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
        } else {
            return $sql;
        }
    }
}
