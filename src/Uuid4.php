<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid;

/**
 * RFC: https://www.rfc-editor.org/rfc/rfc9562.html#name-uuid-version-4
 */
final class Uuid4 implements \Stringable
{
    private static ?\FFI $ffi = null;

    public function __construct(public string $value, bool $validate = true)
    {
        $this->value = strtolower($value);

        if ($validate) {
            self::validate($this->value);
        }
    }

    public static function create(): self
    {
        $bytes = random_bytes(18); // add 2 extra dummy bytes for later performance boost

        $bytes[7] = chr((ord($bytes[7]) & 0b00001111) | 0b01000000); // set ver by nulling 4 MSB and setting 0100 to them, octet 7 is used because of dummy bytes
        $bytes[9] = chr((ord($bytes[9]) & 0b11110011) | 0b00001000); // set var by nulling bits 5 and 6 and setting 10 to them, octet 9 is used because of dummy bytes

        $uuid4 = bin2hex($bytes);
        $uuid4[8] = $uuid4[13] = $uuid4[18] = $uuid4[23] = '-'; // replace 16 (4x4) dummy bits with -

        return new self($uuid4, false);
    }

    /** @return array<self> */
    public static function createManyFfi(int $count): array
    {
        if (self::$ffi === null) {
            if (PHP_OS_FAMILY === 'Windows') {
                $libPath = __DIR__ . '/../include/nwuuid4.dll';
            } elseif (PHP_OS_FAMILY === 'Linux') {
                $libPath = __DIR__ . '/../include/nwuuid4.so';
            } else {
                throw new \Exception(PHP_OS_FAMILY. ' OS in not supported');
            }

            self::$ffi = \FFI::cdef(
                "void generate_uuid4(char *out); void generate_uuid4_batch(char *out, int count);",
                $libPath,
            );
        }

        $buffer = self::$ffi->new("char[" . ($count * 37) . "]"); // 37: 36 bytes of uuid4 format + 1 null byte

        self::$ffi->generate_uuid4_batch($buffer, $count);

        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $result[] = new self(\FFI::string($buffer + ($i * 37), 36), false);
        }

        return $result;
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
