<?php

namespace Framework\Security\RememberMe;

use DateTime;
use stdClass;
use Framework\DAO\AbstractDAO;
use Framework\DAO\Connection;
use Framework\DAO\DAOInterface;
use Framework\DAO\QueryExpression;
use Framework\Security\RememberMe\PersistentToken;

class RememberMeDAO extends AbstractDAO implements DAOInterface
{
    public const SQL_TABLE = 'rememberme_token';
    public const SQL_COLUMNS = [
        'class', 'identifier', 'series', 'value', 'last_used'
    ];

    private QueryExpression $query;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
        $this->query = new QueryExpression();
    }

    public function buildObject(stdClass $object): PersistentToken
    {
        return new PersistentToken(
            $object->p_class,
            $object->p_identifier,
            $object->p_series,
            $object->p_value,
            new DateTime($object->p_last_used)
        );
    }

    public function loadTokenBySeries(string $series): ?PersistentToken
    {
        $this->query->select(self::SQL_COLUMNS, 'p')
            ->from(self::SQL_TABLE, 'p')
            ->where('series = :series')
            ->setParameter('series', $series);

        $token = $this->getOneResult($this, $this->query);

        if ($token instanceof PersistentToken) {
            return $token;
        }

        return null;
    }

    public function deleteTokenBySeries(string $series): void
    {
        $this->delete(self::SQL_TABLE, ['series' => $series]);
    }

    public function deleteTokenByIdentifier(string $identifier): void
    {
        $this->delete(self::SQL_TABLE, ['identifier' => $identifier]);
    }

    public function updateToken(string $series, string $tokenValue, DateTime $lastUsed): void
    {
        $params = [
            'value' => $tokenValue,
            'last_used' => $lastUsed->format('Y-m-d H:i:s')
        ];
        $where = ['series' => $series];
        $this->update(self::SQL_TABLE, $params, $where);
    }

    public function insertToken(PersistentToken $token): void
    {
        $this->insert(self::SQL_TABLE, [
            'class' => $token->getClass(),
            'identifier' => $token->getIdentifier(),
            'series' => $token->getSeries(),
            'value' => $token->getTokenValue(),
            'last_used' => ($token->getLastUsed())->format('Y-m-d H:i:s'),
        ]);
    }
}
