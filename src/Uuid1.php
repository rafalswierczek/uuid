<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid;

final class Uuid1 extends Uuid
{
    public static function validate(string $value): void
    {
        if(!preg_match("/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/", strtolower($value))) {
            throw new \Exception('Invalid UUID v1 format');
        }
    }
}
