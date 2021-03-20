<?php

namespace Framework\Security\RememberMe;

use DateTime;
use Framework\DAO\AbstractDAO;
use Framework\DAO\DAOInterface;
use Framework\DAO\QueryExpression;
use Framework\Security\RememberMe\PersistentToken;

class RememberMeDAO extends AbstractDAO implements DAOInterface
{
    private QueryExpression $query;

    public function __construct()
    {
        $this->query = new QueryExpression();
    }

    public function buildObject(\stdClass $object): PersistentToken
    {
        return new PersistentToken(
            $object->p_class,
            $object->p_username,
            $object->p_series,
            $object->p_value,
            new DateTime($object->p_last_used)
        );
    }

    public function loadTokenBySeries(string $series): ?PersistentToken
    {
        $this->query->select(PersistentToken::SQL_COLUMNS, 'p')
            ->from(PersistentToken::SQL_TABLE, 'p')
            ->where('series = :series')
            ->setParameters([
                'series' => $series
            ]);

        $token = $this->getOneResult($this, $this->query);

        if ($token instanceof PersistentToken) {
            return $token;
        }

        return null;
    }

    public function deleteTokenBySeries(string $series): void
    {
        $params = ['series' => $series];
        $this->delete(PersistentToken::SQL_TABLE, $params);
    }

    public function deleteTokenByUsername(string $username): void
    {
        $params = ['username' => $username];
        $this->delete(PersistentToken::SQL_TABLE, $params);
    }

    public function updateToken(string $series, string $tokenValue, DateTime $lastUsed): void
    {
        $params = [
            'value' => $tokenValue,
            'last_used' => $lastUsed->format('Y-m-d H:i:s')
        ];
        $where = ['series' => $series];
        $this->update(PersistentToken::SQL_TABLE, $params, $where);
    }

    public function insertToken(PersistentToken $token): void
    {
        $this->insert(PersistentToken::SQL_TABLE, [
            'class' => $token->getClass(),
            'username' => $token->getUsername(),
            'series' => $token->getSeries(),
            'value' => $token->getTokenValue(),
            'last_used' => ($token->getLastUsed())->format('Y-m-d H:i:s'),
        ]);
    }
}
