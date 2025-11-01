<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid;

/**
 * RFC: https://www.rfc-editor.org/rfc/rfc9562.html#name-uuid-version-5
 */
final class Uuid5 extends Uuid
{
    public const string NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const string NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const string NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const string NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    public static function create(Uuid $namespace, string $name): self
    {
        $namespaceBin = hex2bin(str_replace('-', '', $namespace->value));

        $hash128bBin = substr(sha1($namespaceBin.$name, true), 0, 16);

        $hash128bBin[6] = chr((ord($hash128bBin[6]) & 0b00001111) | 0b01010000); // set ver by nulling 4 MSB and setting 0101 to them
        $hash128bBin[8] = chr((ord($hash128bBin[8]) & 0b00111111) | 0b10000000); // set var by nulling 2 MSB and setting 10 to them

        $hex = bin2hex($hash128bBin);

        $uuid5 = sprintf(
            '%08s-%04s-%04s-%04s-%012s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        );

        return new self($uuid5, false);
    }

    public static function validate(string $value): void
    {
        if(!preg_match("/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/", strtolower($value))) {
            throw new \Exception('Invalid UUID v5 format');
        }
    }
}
