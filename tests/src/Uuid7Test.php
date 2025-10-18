<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid\Test;

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

        for ($i = 0; $i < 1000000; $i++) {
            Uuid7::validate(Uuid7::create()->value);
        }
    }

    /** This can fail if very unlucky, but it's nothing wrong because such situation won't match the test case name */
    public function testTheSameIn1Ms(): void
    {
        $uuid7A = Uuid7::create();
        $uuid7B = Uuid7::create();

        // the same timestamp, ver, rand_a, var and rand_b_high within 1 ms
        $this->assertSame(substr($uuid7A->value, 0, 24), substr($uuid7B->value, 0, 24));

        // rand_b_low (counter) should increment
        $this->assertTrue(substr($uuid7A->value, 24) < substr($uuid7B->value, 24));
    }

    public function testNotTheSameAfter1Ms(): void
    {
        $uuid7A = Uuid7::create();

        usleep(1000); // sleep 1 ms

        $uuid7B = Uuid7::create();

        // timestamp must be greater after 1 ms
        $this->assertTrue(substr($uuid7A->value, 0, 13) < substr($uuid7B->value, 0, 13));

        // rand_a cannot be the same after 1 ms
        $this->assertNotSame(substr($uuid7A->value, 15, 3), substr($uuid7B->value, 15, 3));

        // var and rand_b_high cannot be the same after 1 ms
        $this->assertNotSame(substr($uuid7A->value, 19, 4), substr($uuid7B->value, 19, 4));

        // rand_b_low cannot be the same after 1 ms
        $this->assertNotSame(substr($uuid7A->value, 24), substr($uuid7B->value, 24));
    }
}
