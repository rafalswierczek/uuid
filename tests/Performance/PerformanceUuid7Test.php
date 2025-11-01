<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid\Test\Performance;

use rafalswierczek\Uuid\Uuid7;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

final class PerformanceUuid7Test extends PerformanceBase
{
    /** @var array<array{library: string, amount: int, timeTotal: string, gen1Ms: float, memUsage: string}> $result */
    private static array $result = [];

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        static::printResult(self::$result, ['Library', 'Amount', 'Time', 'Amount in 1ms', 'Memory usage'], 'UUID v7 generation performance');

        self::$result = []; // needed because GC cannot remove static fields
    }

    public function testCreate1Uuid7Rafalswierczek(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid7::create(); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1, 100_000);
    }

    public function testCreate1KUuid7Rafalswierczek(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid7::create(); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1000, 100);
    }

    public function testCreate1MUuid7Rafalswierczek(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid7::create(); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1_000_000, 10);
    }

    public function testCreate50MUuid7Rafalswierczek(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid7::create(); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 50_000_000, 1);
    }

    public function testCreate1Uuid7Symfony(): void
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v7(); // CPU warm-up
        }

        $this->testCreate('symfony', 1, 100_000);
    }

    public function testCreate1KUuid7Symfony(): void
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v7(); // CPU warm-up
        }

        $this->testCreate('symfony', 1000, 100);
    }

    public function testCreate1MUuid7Symfony(): void
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v7(); // CPU warm-up
        }

        $this->testCreate('symfony', 1_000_000, 10);
    }

    public function testCreate50MUuid7Symfony(): void
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v7(); // CPU warm-up
        }

        $this->testCreate('symfony', 50_000_000, 1);
    }

    public function testCreate1Uuid7Ramsey(): void
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::uuid7(); // CPU warm-up
        }

        $this->testCreate('ramsey', 1, 100_000);
    }

    public function testCreate1KUuid7Ramsey(): void
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::uuid7(); // CPU warm-up
        }

        $this->testCreate('ramsey', 1000, 100);
    }

    public function testCreate1MUuid7Ramsey(): void
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::uuid7(); // CPU warm-up
        }

        $this->testCreate('ramsey', 1_000_000, 10);
    }

    public function testCreate50MUuid7Ramsey(): void
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::uuid7(); // CPU warm-up
        }

        $this->testCreate('ramsey', 50_000_000, 1);
    }

    private function testCreate(string $library, int $amount, int $attempts): void
    {
        $this->expectNotToPerformAssertions();

        $timeSum = 0;
        $memSum = 0;
        $gen1MsSum = 0;

        for ($i = 0; $i < $attempts; $i++) {
            [$end, $mem, $gen1Ms] = $this->performTest($library, $amount);

            $timeSum += $end;
            $memSum += $mem;
            $gen1MsSum += $gen1Ms;
        }

        $timeAvg = static::formatTime($timeSum / $attempts);
        $memUsedAvg = static::formatMemory($memSum / $attempts);
        $gen1MsAvg = round($gen1MsSum / $attempts);

        self::$result[] = [
            'library' => $library,
            'amount' => $amount,
            'timeTotal' => $timeAvg,
            'gen1Ms' => $gen1MsAvg,
            'memUsage' => $memUsedAvg,
        ];
    }

    /** @return array{0: float, 1: int, 2: int} */
    private function performTest(string $library, int $amount): array
    {
        gc_collect_cycles();
        $memStart = memory_get_usage();
        $start = microtime(true);
        $uuidList = []; // this is to check mem usage

        if ($library === 'rafalswierczek') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = Uuid7::create();
            }
        } elseif ($library === 'ramsey') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = RamseyUuid::uuid7();
            }
        } elseif ($library === 'symfony') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = SymfonyUuid::v7();
            }
        }

        $end = microtime(true) - $start;
        $mem = memory_get_usage() - $memStart;

        $uuidListGrouped = [];

        foreach ($uuidList as $uuid7) {
            $timestampHex = substr((string) $uuid7, 0, 13);

            $uuidListGrouped[$timestampHex][] = (string) $uuid7;
        }

        $uuidListGroupedCounts = [];

        foreach ($uuidListGrouped as $group) {
            $uuidListGroupedCounts[] = count($group);
        }

        $gen1Ms = (int) (array_sum($uuidListGroupedCounts) / count($uuidListGrouped));

        unset($uuidList);
        gc_collect_cycles();

        return [$end, $mem, $gen1Ms];
    }
}
