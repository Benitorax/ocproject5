<?php
namespace Config\Security\RememberMe;

use DateTime;
use Exception;
use Config\DAO\AbstractDAO;
use Config\DAO\DAOInterface;

class RememberMeDAO extends AbstractDAO implements DAOInterface
{
    const SQL_TABLE = 'rememberme_token';
    const SQL_SELECT = 'SELECT class, username, series, value, last_used'
        .' FROM rememberme_token';

    public function buildObject(\stdClass $object): PersistentToken
    {
        return new PersistentToken(
            $object->class,
            $object->username,
            $object->series,
            $object->value,
            new DateTime($object->last_used)
        );
    }

    public function loadTokenBySeries(string $series): ?PersistentToken
    {
        $params = ['series' => $series];
        $token = $this->selectOneResultBy(self::SQL_SELECT, $params, $this);

        if ($token instanceof PersistentToken) {
            return $token;
        }

        return null;
    }

    public function deleteTokenBySeries(string $series): void
    {
        $params = ['series' => $series];
        $this->delete(self::SQL_TABLE, $params);
    }

    public function deleteTokenByUsername(string $username): void
    {
        $params = ['username' => $username];
        $this->delete(self::SQL_TABLE, $params);
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
            'username' => $token->getUsername(),
            'series' => $token->getSeries(),
            'value' => $token->getTokenValue(),
            'last_used' => ($token->getLastUsed())->format('Y-m-d H:i:s'),
        ]);
    }
}
