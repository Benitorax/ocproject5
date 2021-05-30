<?php

namespace Framework\DAO;

use PDO;
use PDOStatement;
use Framework\Dotenv\Dotenv;

class Connection
{
    /**
     * Parameters for database connection.
     */
    private array $params;

    private ?PDO $connection = null;

    public function __construct(Dotenv $dotenv)
    {
        // loads config from environment variables
        foreach ($dotenv->all() as $key => $value) {
            if (0 === strpos($key, 'DB_')) {
                $this->params[substr($key, 3)] = $value;
            }
        }
    }

    private function getConnection(): PDO
    {
        if (null !== $this->connection) {
            return $this->connection;
        }

        return $this->connect();
    }

    /**
     * Connects to database.
     */
    private function connect(): PDO
    {
        return $this->connection = new PDO(
            $this->params['HOST'],
            $this->params['USERNAME'],
            $this->params['PASSWORD']
        );
    }

    /**
     * Returns the PDOStatement from query and options.
     * @return false|PDOStatement
     */
    public function prepare(string $query, array $options = [])
    {
        return $this->getConnection()->prepare($query, $options);
    }

    /**
     * Returns the PDOStatement from query and fetchMode.
     *
     * @param int|null $fetchMode
     * @return false|PDOStatement
     */
    public function query(string $query, $fetchMode = null)
    {
        if (null === $fetchMode) {
            return $this->getConnection()->query($query);
        }

        return $this->getConnection()->query($query, $fetchMode);
    }
}
