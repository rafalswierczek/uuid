<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid;

/**
 * RFC: https://www.rfc-editor.org/rfc/rfc9562.html#name-uuid-version-7
 * To keep lexicographical order for each generation during the same timestamp, the Method 2 monotonicity is implemented using static fields
 */
final class Uuid7 implements \Stringable
{
    private const int MAX_INCREMENT = 0xFFFF; // 65535

    private static int $lastTimestamp = 0;
    private static int $verRandA = 0;
    private static int $varRandBHigh = 0;
    private static int $randBLow = 0;

    public function __construct(public string $value, bool $validate = true)
    {
        $this->value = strtolower($value);

        if ($validate) {
            self::validate($this->value);
        }
    }

    public static function create(): self
    {
        if (PHP_INT_SIZE !== 8) {
            throw new \Exception('Only PHP with 64 bit integer is supported');
        }

        $timestamp = (int) (microtime(true) * 1000);

        $timestamp = $timestamp & 0xFFFFFFFFFFFF; // take up to 48 bits of timestamp

        if ($timestamp === self::$lastTimestamp) {
            self::$randBLow += random_int(1, self::MAX_INCREMENT); // increment from 1 to 65535 so that uuid7 with monotonicity is harder to guess
            self::$randBLow = self::$randBLow & 0xFFFFFFFFFFFF; // keep rand_b low at max of 48 bits, start from 0 when overflow
        } else {
            self::$randBLow = random_int(0, 0xFFFF0001FFFE); // from 0 to (48 bit uint - (MAX_INCREMENT * MAX_INCREMENT)), this is to always allow MAX_INCREMENT increments of the same numer which is MAX_INCREMENT
            self::$varRandBHigh = self::getVarRandBHigh();
            self::$verRandA = self::getVerRandA();
        }

        $unixTsMs = str_pad(dechex($timestamp), 12, '0', STR_PAD_LEFT); // unix_ts_ms
        
        $uuid7 = sprintf(
            '%s-%s-%s-%s-%s',
            substr($unixTsMs, 0, 8),
            substr($unixTsMs, 8, 4),
            dechex(self::$verRandA), // ver and rand_a, always 16 bits because dechex handles 0 bit padding
            dechex(self::$varRandBHigh), // var and rand_b high, always 16 bits
            str_pad(dechex(self::$randBLow), 12, '0', STR_PAD_LEFT), // rand_b low (counter)
        );

        self::$lastTimestamp = $timestamp;
        
        return new self($uuid7, false);
    }

    public static function validate(string $value): void
    {
        if (!preg_match("/^[0-9a-f]{8}\-[0-9a-f]{4}\-7[0-9a-f]{3}\-[89ab][0-9a-f]{3}\-[0-9a-f]{12}$/", strtolower($value))) {
            throw new \Exception('Invalid UUID v7 format');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $uuid7): bool
    {
        return $uuid7->value === $this->value;
    }

    /** @return int 15 bit version with rand_a */
    private static function getVerRandA(): int
    {
        $ver = 0b111 << 12; // version with space for rand_a
        $randA = random_int(0, 0xFFF); // from 1 to 12 bits

        return $ver | $randA; // add rand_a to LSB of ver
    }

    /** @return int 16 bit var with left part of rand_b */
    private static function getVarRandBHigh(): int
    {
        $var = 0b10 << 14; // var with space for 14 MSB of rand_b
        $randBHigh = random_int(0, 0xFFFF) & 0b11111111111111; // from 0 to 14 bits

        return $var | $randBHigh; // add 14 MSB of rand_b to LSB of var
    }
}
