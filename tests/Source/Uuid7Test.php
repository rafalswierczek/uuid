<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid\Test\Source;

use PHPUnit\Framework\TestCase;
use rafalswierczek\Uuid\Uuid7;

final class Uuid7Test extends TestCase
{
    public const VALID_UUID7 = '0199d59e-8041-7b74-b74e-b94310cd9473';

    public function testNewInstanceAsString(): void
    {
        $uuid7 = new Uuid7(strtoupper(self::VALID_UUID7));

        $this->assertSame(self::VALID_UUID7, (string) $uuid7);
    }

    public function testEquals(): void
    {
        $uuid7A = new Uuid7(self::VALID_UUID7);
        $uuid7B = new Uuid7(strtoupper(self::VALID_UUID7));

        $this->assertTrue($uuid7A->equals($uuid7B));
        $this->assertTrue($uuid7B->equals($uuid7A));
    }

    public function testNotEquals(): void
    {
        $uuid7A = Uuid7::create();
        $uuid7B = Uuid7::create();

        $this->assertFalse($uuid7A->equals($uuid7B));
        $this->assertFalse($uuid7B->equals($uuid7A));
    }

    public function testInvalidUuid(): void
    {
        $this->expectException(\Exception::class);

        new Uuid7('0199d59e-8041-4b74-b74e-b94310cd9473');
    }

    public function testValidateMany(): void
    {
        $this->expectNotToPerformAssertions();

        for ($i = 0; $i < 1_000_000; $i++) {
            Uuid7::validate(Uuid7::create()->value);
        }
    }

    public function testMonotonicity(): void
    {
        $uuids = [];

        for ($i = 0; $i < 1_000_000; $i++) {
            $uuids[] = Uuid7::create();
        }

        $sorted = $uuids;
        sort($sorted);

        $this->assertSame($sorted, $uuids);
    }
}
