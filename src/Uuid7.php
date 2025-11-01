<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid;

/**
 * RFC: https://www.rfc-editor.org/rfc/rfc9562.html#name-uuid-version-7
 * To keep lexicographical order for each generation during the same timestamp, the Method 3 of monotonicity is implemented
 */
final class Uuid7 extends Uuid
{
    public static function create(): self
    {
        if (PHP_INT_SIZE !== 8) {
            throw new \Exception('Only PHP with 64 bit integer is supported');
        }

        $timestampMs = microtime(true) * 1000;
        $timestampMicro = round($timestampMs * 1000) % 1000; // get only microseconds part of the timestamp, for example: $timestampMs = float(1761309765039.0168), $timestampMicro = int(17)
        $timestampMs = (int) $timestampMs;

        $varRandB = (0b10 << 62) | random_int(0, 0x3FFFFFFFFFFFFFFF); // 2 bits var and 62 bits rand_b, always 64 bits

        $uuid7 = sprintf(
            '%08x-%04x-%04x-%04x-%012x',
            $timestampMs >> 16, // get all leftmost bits before 16 LSB and left-pad them with 0 using sprintf to get 32 bits
            $timestampMs & 0xFFFF, // get up to 16 LSB of the timestamp, left-pad with 0 to get 16 bits
            (0b111 << 12) | ($timestampMicro & 0xFFF), // create 15 bits buffer with version (111) as MSB and then fill its LSB with 12 LSB of microseconds, left-pad with 0 to get 16 bits
            ($varRandB >> 48) & 0xFFFF, // get 16 MSB (64-48) of rand_b, var in first 2 MSB
            $varRandB & 0xFFFFFFFFFFFF, // get 48 LSB of rand_b
        );

        return new self($uuid7, false);
    }

    public static function validate(string $value): void
    {
        if (!preg_match("/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/", strtolower($value))) {
            throw new \Exception('Invalid UUID v7 format');
        }
    }
}
