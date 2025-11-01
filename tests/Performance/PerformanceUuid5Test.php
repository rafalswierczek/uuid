<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid\Test\Performance;

use rafalswierczek\Uuid\Uuid;
use rafalswierczek\Uuid\Uuid1;
use rafalswierczek\Uuid\Uuid5;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface as RamseyUuidInterface;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

final class PerformanceUuid5Test extends PerformanceBase
{
    private static Uuid $namespaceRafalswierczek;
    private static SymfonyUuid $namespaceSymfony;
    private static RamseyUuidInterface $namespaceRamsey;
    /** @var array<array{library: string, amount: int, timeTotal: string, memUsage: string}> $result */
    private static array $result = [];

    public static function setUpBeforeClass(): void
    {
        self::$namespaceRafalswierczek = new Uuid1(Uuid5::NAMESPACE_DNS);
        self::$namespaceSymfony = SymfonyUuid::fromString(SymfonyUuid::NAMESPACE_DNS);
        self::$namespaceRamsey = RamseyUuid::fromString(RamseyUuid::NAMESPACE_DNS);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        static::printResult(self::$result, ['Library', 'Amount', 'Time', 'Memory usage'], 'UUID v4 generation performance');

        self::$result = []; // needed because GC cannot remove static fields
    }

    public function testCreate1Uuid5Rafalswierczek(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid5::create(self::$namespaceRafalswierczek, 'seed'); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1, 100_000);
    }

    public function testCreate1KUuid5Rafalswierczek(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid5::create(self::$namespaceRafalswierczek, 'seed'); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1000, 100);
    }

    public function testCreate1MUuid5Rafalswierczek(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid5::create(self::$namespaceRafalswierczek, 'seed'); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1_000_000, 10);
    }

    public function testCreate50MUuid5Rafalswierczek(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid5::create(self::$namespaceRafalswierczek, 'seed'); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 50_000_000, 1);
    }

    public function testCreate1Uuid5Symfony(): void
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v5(self::$namespaceSymfony, 'seed'); // CPU warm-up
        }

        $this->testCreate('symfony', 1, 100_000);
    }

    public function testCreate1KUuid5Symfony(): void
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v5(self::$namespaceSymfony, 'seed'); // CPU warm-up
        }

        $this->testCreate('symfony', 1000, 100);
    }

    public function testCreate1MUuid5Symfony(): void
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v5(self::$namespaceSymfony, 'seed'); // CPU warm-up
        }

        $this->testCreate('symfony', 1_000_000, 10);
    }

    public function testCreate50MUuid5Symfony(): void
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v5(self::$namespaceSymfony, 'seed'); // CPU warm-up
        }

        $this->testCreate('symfony', 50_000_000, 1);
    }

    public function testCreate1Uuid5Ramsey(): void
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::Uuid5(self::$namespaceRamsey, 'seed'); // CPU warm-up
        }

        $this->testCreate('ramsey', 1, 100_000);
    }

    public function testCreate1KUuid5Ramsey(): void
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::Uuid5(self::$namespaceRamsey, 'seed'); // CPU warm-up
        }

        $this->testCreate('ramsey', 1000, 100);
    }

    public function testCreate1MUuid5Ramsey(): void
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::Uuid5(self::$namespaceRamsey, 'seed'); // CPU warm-up
        }

        $this->testCreate('ramsey', 1_000_000, 10);
    }

    public function testCreate50MUuid5Ramsey(): void
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::Uuid5(self::$namespaceRamsey, 'seed'); // CPU warm-up
        }

        $this->testCreate('ramsey', 50_000_000, 1);
    }

    private function testCreate(string $library, int $amount, int $attempts): void
    {
        $this->expectNotToPerformAssertions();

        $timeSum = 0;
        $memSum = 0;

        for ($i = 0; $i < $attempts; $i++) {
            [$end, $mem] = $this->performTest($library, $amount);

            $timeSum += $end;
            $memSum += $mem;
        }

        $timeAvg = static::formatTime($timeSum / $attempts);
        $memUsedAvg = static::formatMemory($memSum / $attempts);

        self::$result[] = [
            'library' => $library,
            'amount' => $amount,
            'timeTotal' => $timeAvg,
            'memUsage' => $memUsedAvg,
        ];
    }

    /** @return array{0: float, 1: int} */
    private function performTest(string $library, int $amount): array
    {
        gc_collect_cycles();
        $memStart = memory_get_usage();
        $start = microtime(true);
        $uuidList = []; // this is to check mem usage

        if ($library === 'rafalswierczek') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = Uuid5::create(self::$namespaceRafalswierczek, 'seed');
            }
        } elseif ($library === 'ramsey') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = RamseyUuid::Uuid5(self::$namespaceRamsey, 'seed');
            }
        } elseif ($library === 'symfony') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = SymfonyUuid::v5(self::$namespaceSymfony, 'seed');
            }
        }

        $end = microtime(true) - $start;
        $mem = memory_get_usage() - $memStart;

        unset($uuidList);
        gc_collect_cycles();

        return [$end, $mem];
    }
}
