<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid4\Test;

use rafalswierczek\Uuid\Test\PerformanceBase;
use rafalswierczek\Uuid\Uuid4;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

final class PerformanceUuid4Test extends PerformanceBase
{
    private static array $result = [];

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        static::printResult(self::$result, ['Library', 'Amount', 'Time', 'Memory usage'], 'UUID v4 generation performance');

        self::$result = []; // needed because GC cannot remove static fields
    }

    public function testCreate1()
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid4::createManyFfi(1); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1, 100_000);
    }

    public function testCreate1K()
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid4::createManyFfi(1); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1000, 100);
    }

    public function testCreate1M()
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid4::createManyFfi(1); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 1_000_000, 10);
    }

    public function testCreate50M()
    {
        for ($i = 0; $i < 100; $i++) {
            Uuid4::createManyFfi(1); // CPU warm-up
        }

        $this->testCreate('rafalswierczek', 50_000_000, 1);
    }

    public function testCreate1Symfony()
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v4(); // CPU warm-up
        }

        $this->testCreate('symfony', 1, 100_000);
    }

    public function testCreate1KSymfony()
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v4(); // CPU warm-up
        }

        $this->testCreate('symfony', 1000, 100);
    }

    public function testCreate1MSymfony()
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v4(); // CPU warm-up
        }

        $this->testCreate('symfony', 1_000_000, 10);
    }

    public function testCreate50MSymfony()
    {
        for ($i = 0; $i < 100; $i++) {
            SymfonyUuid::v4(); // CPU warm-up
        }

        $this->testCreate('symfony', 50_000_000, 1);
    }

    public function testCreate1Ramsey()
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::uuid4(); // CPU warm-up
        }

        $this->testCreate('ramsey', 1, 100_000);
    }

    public function testCreate1KRamsey()
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::uuid4(); // CPU warm-up
        }

        $this->testCreate('ramsey', 1000, 100);
    }

    public function testCreate1MRamsey()
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::uuid4(); // CPU warm-up
        }

        $this->testCreate('ramsey', 1_000_000, 10);
    }

    public function testCreate50MRamsey()
    {
        for ($i = 0; $i < 100; $i++) {
            RamseyUuid::uuid4(); // CPU warm-up
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

    private function performTest(string $library, int $amount): array
    {
        gc_collect_cycles();
        $memStart = memory_get_usage();
        $start = microtime(true);
        $uuidList = []; // this is to check mem usage

        if ($library === 'rafalswierczek') {
            $uuidList = Uuid4::createManyFfi($amount);
        } elseif ($library === 'ramsey') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = RamseyUuid::uuid4();
            }
        } elseif ($library === 'symfony') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = SymfonyUuid::v4();
            }
        }

        $end = microtime(true) - $start;
        $mem = memory_get_usage() - $memStart;

        unset($uuidList);
        gc_collect_cycles();

        return [$end, $mem];
    }
}
