<?php
namespace Config\DAO;

use PDO;
use Exception;

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

        catch(Exception $errorConnection)
        {
            die ('Erreur de connection :'.$errorConnection->getMessage());
        }

    }

    protected function createQuery($sql, $parameters = null)
    {
        if($parameters)
        {
            $result = $this->checkConnection()->prepare($sql);
            $result->setFetchMode(PDO::FETCH_CLASS, static::class);

            foreach($parameters as $key => $value) {
                $result->bindValue(':'.$key, $value);
            }
            
            $result->execute($parameters);

            return $result;
        }

        $result = $this->checkConnection()->query($sql);
        $result->setFetchMode(PDO::FETCH_CLASS, static::class);

        return $result;
    }
}