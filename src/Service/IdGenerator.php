<?php
namespace App\Service;

class IdGenerator
{
    const MIN_PREFIX = 10000;
    const MAX_PREFIX = 99999;
    
    public static function generate(): string
    {
        return uniqid((string) rand(self::MIN_PREFIX, self::MAX_PREFIX))
            . uniqid((string) rand(self::MIN_PREFIX, self::MAX_PREFIX));
    }
}
