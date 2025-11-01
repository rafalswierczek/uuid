<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid;

abstract class Uuid implements \Stringable
{
    public function __construct(public string $value, bool $validate = true)
    {
        $this->value = strtolower($value);

        if ($validate) {
            static::validate($this->value);
        }
    }

    public function equals(self $uuid): bool
    {
        return $uuid->value === $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public abstract static function validate(string $value): void;
}
