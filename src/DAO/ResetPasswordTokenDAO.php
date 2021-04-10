<?php

namespace App\DAO;

use DateTime;
use App\Model\User;
use Ramsey\Uuid\Uuid;
use Framework\DAO\AbstractDAO;
use App\Model\ResetPasswordToken;
use Framework\DAO\QueryExpression;
use DateTimeImmutable;

class ResetPasswordTokenDAO extends AbstractDAO
{
    public const SQL_TABLE = 'reset_password_token';
    public const SQL_COLUMNS = [
        'id', 'user_id', 'selector', 'hashed_token', 'request_at', 'expired_at'
    ];

    private QueryExpression $query;

    /**
     * Returns an User object from stdClass.
     */
    public function buildObject(\stdClass $o): ResetPasswordToken
    {
        $user = new User();

        if (!empty($o->u_id)) {
            $user->setId($o->u_id)
            ->setUuid(Uuid::fromString($o->u_uuid))
            ->setEmail($o->u_email)
            ->setPassword($o->u_password)
            ->setUsername($o->u_username)
            ->setCreatedAt(new DateTime($o->u_created_at))
            ->setUpdatedAt(new DateTime($o->u_updated_at))
            ->setRoles(json_decode($o->u_roles))
            ->setIsBlocked($o->u_is_blocked);
        }

        $token = new ResetPasswordToken($user, new DateTimeImmutable($o->r_expired_at), $o->r_selector, $o->r_hashed_token);
        $token->setRequestedAt(new DateTimeImmutable($o->r_requested_at));

        return $token;
    }

    /**
     * @return null|object|ResetPasswordToken the object is instance of ResetPasswordToken class
     */
    public function getOneBySelector(string $selector)
    {
        $this->prepareQuery()
            ->where('r.selector = :selector')
            ->setParameter('selector', $selector);

        return $this->getOneResult($this, $this->query);
    }

    /**
     * Sets the select, table and jointure for the sql query.
     */
    private function prepareQuery(): QueryExpression
    {
        return $this->query = (new QueryExpression())
            ->select(self::SQL_COLUMNS, 'r')
            ->addSelect(UserDAO::SQL_COLUMNS, 'u')
            ->from(self::SQL_TABLE, 'r')
            ->leftOuterJoin(UserDAO::SQL_TABLE, 'u', 'user_id = u.id');
    }

    /**
     * Inserts a new row in the database.
     */
    public function add(ResetPasswordToken $token): void
    {
        $this->insert('reset_password_token', [
            'user_id' => $token->getUser()->getId(),
            'selector' => $token->getSelector(),
            'hashed_token' => $token->getHashedToken(),
            'request_at' => $token->getRequestedAt()->format('Y-m-d H:i:s'),
            'expired_at' => $token->getExpiredAt()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Deletes row(s) by user id.
     */
    public function deleteByUserId(int $userId): void
    {
        $this->delete(self::SQL_TABLE, ['user_id' => $userId]);
    }
}
