<?php

namespace Framework\DAO;

class SqlGenerator
{
    public static function generateColumnWithAlias(string $alias, array $columnNames): string
    {
        return implode(', ', self::withAlias($alias, $columnNames));
    }

    public static function withAlias(string $alias, array $columnNames): array
    {
        return array_map(function ($element) use ($alias) {
            return $alias . '.' . $element . ' ' . $alias . '_' . $element;
        }, $columnNames);
    }
}
