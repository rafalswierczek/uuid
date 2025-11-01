<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid\Test\Source;

use PHPUnit\Framework\TestCase;
use rafalswierczek\Uuid\Uuid1;
use rafalswierczek\Uuid\Uuid5;

final class Uuid5Test extends TestCase
{
    public const VALID_UUID5 = '0bfa18dd-3e8a-5810-b9b2-336b56af84b2';

    public function testNewInstanceAsString(): void
    {
        $uuid5 = new Uuid5(strtoupper(self::VALID_UUID5));

        $this->assertSame(self::VALID_UUID5, (string) $uuid5);
    }

    public function testEquals(): void
    {
        $uuid5A = new Uuid5(self::VALID_UUID5);
        $uuid5B = new Uuid5(strtoupper(self::VALID_UUID5));

        $this->assertTrue($uuid5A->equals($uuid5B));
        $this->assertTrue($uuid5B->equals($uuid5A));
    }

    public function testEqualsInTheSameNamespace(): void
    {
        $namespace = new Uuid1(Uuid5::NAMESPACE_DNS);
        $uuid5A = Uuid5::create($namespace, 'seed');
        $uuid5B = Uuid5::create($namespace, 'seed');

        $this->assertTrue($uuid5A->equals($uuid5B));
        $this->assertTrue($uuid5B->equals($uuid5A));
    }

    public function testNotEqualsInTheSameNamespace(): void
    {
        $namespace = new Uuid1(Uuid5::NAMESPACE_DNS);
        $uuid5A = Uuid5::create($namespace, 'seed1');
        $uuid5B = Uuid5::create($namespace, 'seed2');

        $this->assertFalse($uuid5A->equals($uuid5B));
        $this->assertFalse($uuid5B->equals($uuid5A));
    }

    public function testNotEqualsForTheSameSeedInDifferentNamespace(): void
    {
        $namespace1 = new Uuid1(Uuid5::NAMESPACE_DNS);
        $namespace2 = new Uuid1(Uuid5::NAMESPACE_URL);
        $uuid5A = Uuid5::create($namespace1, 'seed');
        $uuid5B = Uuid5::create($namespace2, 'seed');

        $this->assertFalse($uuid5A->equals($uuid5B));
        $this->assertFalse($uuid5B->equals($uuid5A));
    }

    public function testNotEqualsForDifferentSeedInDifferentNamespace(): void
    {
        $namespace1 = new Uuid1(Uuid5::NAMESPACE_DNS);
        $namespace2 = new Uuid1(Uuid5::NAMESPACE_URL);
        $uuid5A = Uuid5::create($namespace1, 'seed1');
        $uuid5B = Uuid5::create($namespace2, 'seed2');

        $this->assertFalse($uuid5A->equals($uuid5B));
        $this->assertFalse($uuid5B->equals($uuid5A));
    }

    public function testInvalidUuid(): void
    {
        $this->expectException(\Exception::class);

        new Uuid5('0bfa18dd-3e8a-5810-c9b2-336b56af84b2');
    }

    public function testValidateMany(): void
    {
        $this->expectNotToPerformAssertions();

        $namespace = new Uuid1(Uuid5::NAMESPACE_DNS);

        for ($i = 0; $i < 1000000; $i++) {
            Uuid5::validate(Uuid5::create($namespace, "seed$i")->value);
        }
    }
}
