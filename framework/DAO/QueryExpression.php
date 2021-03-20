<?php

namespace Framework\DAO;

use Framework\DAO\SqlGenerator;

/**
 * Contains the SELECT SQL expression.
 *
 * e.g. 'SELECT Id, user'
 */
class QueryExpression
{
    private ?string $select = null;
    private ?string $from = null;
    private ?string $join = null;
    private ?string $where = null;
    private ?string $orderBy = null;
    private ?string $limit = null;
    private array $parameters = [];

    public function select(array $columnNames, string $alias): self
    {
        $this->select = 'SELECT ' . SqlGenerator::generateColumnWithAlias($alias, $columnNames);

        return $this;
    }

    public function from(string $from, string $alias): self
    {
        $this->from = 'FROM ' . $from . ' ' . $alias;

        return $this;
    }

    public function addSelect(array $columnNames, string $alias): self
    {
        if (null === $this->select) {
            $this->select($columnNames, $alias);
        } else {
            $this->select .= ', ' . SqlGenerator::generateColumnWithAlias($alias, $columnNames);
        }

        return $this;
    }

    public function leftOuterJoin(string $table, string $alias, string $on): self
    {
        $this->join = 'LEFT OUTER JOIN ' . $table . ' ' . $alias . ' ON ' . $on;

        return $this;
    }

    public function addLeftOuterJoin(string $table, string $alias, string $on): self
    {
        if (null === $this->join) {
            $this->leftOuterJoin($table, $alias, $on);
        } else {
            $this->join .= ' LEFT OUTER JOIN ' .  $table . ' ' . $alias . ' ON ' . $on;
            ;
        }

        return $this;
    }

    public function where(string $where): self
    {
        $this->where = 'WHERE (' . $where . ')';

        return $this;
    }

    public function addWhere(string $where): self
    {
        if (null === $this->where) {
            $this->where($where);
        } else {
            $this->where .= ' AND (' . $where . ')';
        }

        return $this;
    }

    public function orWhere(string $where): self
    {
        if (null === $this->where) {
            $this->where($where);
        } else {
            $this->where .= ' OR (' . $where . ')';
        }

        return $this;
    }

    public function orderBy(string $columnName, string $sort): self
    {
        $this->orderBy = 'ORDER BY ' . $columnName . ' ' . $sort;

        return $this;
    }

    public function addOrderBy(string $columnName, string $sort): self
    {
        if (null === $this->orderBy) {
            $this->orderBy($columnName, $sort);
        } else {
            $this->orderBy .= ', ' . $columnName . ' ' . $sort;
        }

        return $this;
    }

    public function limit(int $offset, int $range): self
    {
        $this->limit = 'LIMIT ' . $offset . ', ' . $range;

        return $this;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * @param int|string|bool $value
     */
    public function setParameter(string $name, $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function generateSQL(): string
    {
        return  $this->select
                . ' ' . $this->from
                . ' ' . $this->join
                . ' ' . $this->where
                . ' ' . $this->orderBy
                . ' ' . $this->limit;
    }

    public function generateCountSQL(): string
    {
        return  'SELECT count(*)'
                . ' ' . $this->from
                . ' ' . $this->join
                . ' ' . $this->where
                . ' ' . $this->orderBy
                . ' ' . $this->limit;
    }
}
