<?php
namespace Config\DAO;

use PDO;
use Exception;
use stdClass;

abstract class AbstractDAO
{
    const HOST = 'localhost';
    const DB_NAME = 'ocproject5';
    const CHARSET = 'utf8';
    const DB_HOST = 'mysql:host='.self::HOST.';dbname='.self::DB_NAME.';charset='.self::CHARSET;
    const DB_USER = 'root';
    const DB_PASS = '';

    private $connection;

    private function checkConnection()
    {
        if($this->connection === null) {
            return $this->getConnection();
        }

        return $this->connection;
    }

    private function getConnection()
    {
        try{
            $this->connection = new PDO(self::DB_HOST, self::DB_USER, self::DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connection;
        }

        catch(Exception $connectionError)
        {
            die ('Connection error:'.$connectionError->getMessage());
        }

    }

    protected function createQuery($sql, $parameters = null)
    {
        if($parameters)
        {
            $stmt = $this->checkConnection()->prepare($sql);

            foreach($parameters as $key => $value) {
                $stmt->bindValue(':'.$key, $value);
            }
            
            $stmt->execute($parameters);

            return $stmt;

        } else {
            $stmt = $this->checkConnection()->query($sql);

            return $stmt;    
        }
    }

    /**
     * var $parameters = [id => $id, username => $username]
     * return <object> A model object
     */
    public function selectOneResultBy(string $sqlPrefix, array $parameters, DAOInterface $dao)
    {
        $stmt = $this->select($sqlPrefix, $parameters);
        $result = $stmt->fetchObject(stdClass::class);
        $stmt->closeCursor();

        return $dao->buildObject($result);
    }

    /**
     * var $parameters = [id => $id, username => $username]
     * return <object> A model object
     */
    public function selectResultBy(string $sqlPrefix, array $parameters, DAOInterface $dao)
    {
        $stmt = $this->select($sqlPrefix, $parameters);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, stdClass::class);
        $stmt->closeCursor();

        $objects = [];
        foreach ($result as $row){
            $objects[] = $dao->buildObject($row);
        } 

        return $objects;
    }

    public function selectAll(string $sqlPrefix, DAOInterface $dao)
    {
        $stmt = $this->select($sqlPrefix);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, stdClass::class);
        $stmt->closeCursor();

        $objects = [];
        foreach ($result as $row){
            $objects[] = $dao->buildObject($row);
        } 

        return $objects;
    }

    /**
     * var $parameters = [id => $id, username => $username]
     * return <object> A model object
     */
    public function select(string $sql, array $parameters = null, array $orderBy = null)
    {
        if($parameters) {
            $sql = $this->addWhere($sql, $parameters);
        }
        $sql = $this->addOrderBy($sql, $orderBy);
        
        return $this->createQuery($sql, $parameters);
    }

    public function insert(string $tableName, array $parameters)
    {
        [$colNameString, $paramString] = $this->paramsToStrings($parameters);

        $sql = 'INSERT INTO '.$tableName.' ('.$colNameString.') VALUES ('.$paramString.')';
        $this->createQuery($sql, $parameters);
    }

    private function paramsToStrings(array $parameters): array
    {
        $i = 1;
        $colNameString = '';
        $paramString = '';

        foreach($parameters as $key => $value) {
            if($i < count($parameters)) {
                $colNameString .= $key.', ';
                $paramString .= ':'.$key.', ';
            } else {
                $colNameString .= $key;
                $paramString .= ':'.$key;
            }
            $i++;
        }

        return [$colNameString, $paramString];
    }

    private function addWhere(string $sqlPrefix, array $parameters): string
    {
        $i = 1;
        $where = ' WHERE ';

        foreach($parameters as $key => $value) {
            if($i < count($parameters)) {
                $where .= $key.' = :'.$key.' AND ';
            } else {
                $where .= $key.' = :'.$key;
            }
            $i++;
        }

        return $sqlPrefix . $where;
    }

    private function addOrderBy(string $sql, array $orderBy = null): string
    {
        if($orderBy) {
            $i = 1;
            $order = ' ORDER BY ';

            foreach($orderBy as $key => $value) {
                if($i < count($orderBy)) {
                    $order .= $key.' '.$value.', ';
                } else {
                    $order .= $key.' '.$value;
                }
                $i++;
            }
    
            return $sql.$order;
        } else {
            return $sql.' ORDER BY updated_at DESC';
        }
    }
}