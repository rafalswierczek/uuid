<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid\Test;

use rafalswierczek\Uuid\Uuid7;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

ini_set('memory_limit', '8192M');

final class Uuid7Test extends TestCase
{
    public const VALID_UUID7 = '0199d59e-8041-7b74-b74e-b94310cd9473';

    private static array $result = [];

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::printResult();
    }

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

    public function testCreatePerformance10M(): void
    {
        $this->expectNotToPerformAssertions();

        $this->performTest('rafalswierczek', 10000000);
    }

    public function testCreatePerformance10MRamsey(): void
    {
        $this->expectNotToPerformAssertions();

        $this->performTest('ramsey', 10000000);
    }

    public function testCreatePerformance1M(): void
    {
        $this->expectNotToPerformAssertions();

        $this->performTest('rafalswierczek', 1000000);
    }

    public function testCreatePerformance1MRamsey(): void
    {
        $this->expectNotToPerformAssertions();

        $this->performTest('ramsey', 1000000);
    }

    public function testCreatePerformance1000(): void
    {
        $this->expectNotToPerformAssertions();

        $this->performTest('rafalswierczek', 1000);
    }

    public function testCreatePerformance1000Ramsey(): void
    {
        $this->expectNotToPerformAssertions();

        $this->performTest('ramsey', 1000);
    }

    public function testCreatePerformance1(): void
    {
        $this->expectNotToPerformAssertions();

        $this->performTest('rafalswierczek', 1);
    }

    public function testCreatePerformance1Ramsey(): void
    {
        $this->expectNotToPerformAssertions();

        $this->performTest('ramsey', 1);
    }

    private function performTest(string $library, int $amount): void
    {
        $mem = memory_get_usage();
        $start = microtime(true);
        $uuidList = [];

        if ($library === 'rafalswierczek') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = Uuid7::create();
            }
        } elseif ($library === 'ramsey') {
            for ($i = 0; $i < $amount; $i++) {
                $uuidList[] = Uuid::uuid7();
            }
        }

        $time = substr(sprintf('%12.10f', microtime(true) - $start), 0, 12);
        $memUsed = memory_get_usage() - $mem;

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

        self::$result[] = [
            'library' => $library,
            'amount' => $amount,
            'timeTotal' => $time,
            'gen1Ms' => $gen1Ms,
            'memUsage' => substr(sprintf('%8.7f', $memUsed / 1024 / 1024), 0, 9) . ' MiB',
        ];
    }

    private static function printResult(): void
    {
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders(['Library', 'Amount', 'Time seconds', 'Amount 1ms', 'Memory usage'])
            ->setRows(self::$result);

        $output->writeln('');
        $output->writeln('<info>UUID v7 generation performance, <options=bold>rafalswierczek/uuid</> vs <options=bold>ramsey/uuid</></info>');
        $table->render();
    }
}
