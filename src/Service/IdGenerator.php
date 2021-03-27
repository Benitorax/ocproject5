<?php

namespace App\Service;

class IdGenerator
{
    private const MIN_PREFIX = 10000;
    private const MAX_PREFIX = 99999;

    public static function generate(): string
    {
        return uniqid((string) mt_rand(self::MIN_PREFIX, self::MAX_PREFIX))
            . uniqid((string) mt_rand(self::MIN_PREFIX, self::MAX_PREFIX));
    }
}
