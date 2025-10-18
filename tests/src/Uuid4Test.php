<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid\Test;

use rafalswierczek\Uuid\Uuid4;

final class Uuid4Test extends PerformanceBase
{
    public const VALID_UUID4 = 'f3d7fa06-d938-4c22-9505-c585efa381df';

    public function testNewInstanceAsString(): void
    {
        $uuid4 = new Uuid4(strtoupper(self::VALID_UUID4));

        $this->assertSame(self::VALID_UUID4, (string) $uuid4);
    }

    public function testEquals(): void
    {
        $uuid4A = new Uuid4(self::VALID_UUID4);
        $uuid4B = new Uuid4(strtoupper(self::VALID_UUID4));

        $this->assertTrue($uuid4A->equals($uuid4B));
        $this->assertTrue($uuid4B->equals($uuid4A));
    }

    public function testNotEquals(): void
    {
        $uuid4A = Uuid4::create();
        $uuid4B = Uuid4::create();

        $this->assertFalse($uuid4A->equals($uuid4B));
        $this->assertFalse($uuid4B->equals($uuid4A));
    }

    public function testInvalidUuid(): void
    {
        $this->expectException(\Exception::class);

        new Uuid4('f3d7fa06-d938-7c22-9505-c585efa381df');
    }

    public function testValidateMany(): void
    {
        $this->expectNotToPerformAssertions();

        for ($i = 0; $i < 1000000; $i++) {
            Uuid4::validate(Uuid4::create()->value);
        }
    }

    public function testValidateManyFfi(): void
    {
        $this->expectNotToPerformAssertions();

        $uuids = Uuid4::createManyFfi(1000000);

        foreach ($uuids as $uuid4) {
            Uuid4::validate($uuid4->value);
        }
    }
}
