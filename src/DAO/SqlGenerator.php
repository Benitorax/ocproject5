<?php

namespace App\DAO;

class SQLGenerator
{
    public function generateStringWithAlias(string $alias, array $columnNames): string
    {
        return implode(', ', $this->withAlias($alias, $columnNames));
    }

    public function withAlias(string $alias, array $columnNames): array
    {
        return array_map(function ($element) use ($alias) {
            return $alias . '.' . $element . ' ' . $alias . '_' . $element;
        }, $columnNames);
    }
}
