<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid;

/**
 * RFC: https://datatracker.ietf.org/doc/html/rfc4122#section-4.4
 */
final class Uuid4 implements \Stringable
{
    public function __construct(public string $value, bool $validate = true)
    {
        $this->value = strtolower($value);

        if ($validate) {
            self::validate($this->value);
        }
    }

    public static function create(): self
    {
        $bytes = random_bytes(16);
        
        // set 2 MSB of clock_seq_hi_and_reserved to 00 in octet 8 | keep 6 LSB the same
        $reset_clock_seq_hi_and_reserved = ord($bytes[8]) & 0b00111111;
        
        // add 10 to 2 MSB | keep 6 LSB the same
        $bytes[8] = chr($reset_clock_seq_hi_and_reserved | 0b10000000);
        
        // set 4 MSB of time_hi_and_version to 0000 in octet 6 | keep 4 LSB the same | 4 MSB are only available in octet 6
        $reset_time_hi_and_version = ord($bytes[6]) & 0b00001111;
        
        // add 0100 to 4 MSB | keep 4 LSB the same 
        $bytes[6] = chr($reset_time_hi_and_version | 0b01000000);
        
        $hexString = bin2hex($bytes);
        
        $uuid4 = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hexString, 4));
        
        return new self($uuid4, false);
    }

    public static function validate(string $value): void
    {
        if(!preg_match("/^[0-9a-f]{8}\-[0-9a-f]{4}\-4[0-9a-f]{3}\-[89ab][0-9a-f]{3}\-[0-9a-f]{12}$/", strtolower($value))) {
            throw new \Exception('Invalid UUID v4 format');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $uuid4): bool
    {
        return $uuid4->value === $this->value;
    }
}
