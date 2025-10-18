<?php

declare(strict_types=1);

namespace rafalswierczek\Uuid\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class PerformanceBase extends TestCase
{
    protected static function formatTime(float $end): string
    {
        if ($end >= 1) {
            $time = sprintf('%.2f sec', $end);
        } elseif ($end >= 0.001) {
            $time = sprintf('%.2f ms', $end * 1000);
        } else {
            $time = sprintf('%.2f µs', $end * 1_000_000);
        }

        return $time;
    }

    protected static function formatMemory(float $bytes): string
    {
        if ($bytes < 1024) {
            return number_format($bytes, 2) . ' B';
        } elseif ($bytes < 1048576) {
            return number_format($bytes / 1024, 2) . ' KiB';
        } elseif ($bytes < 1073741824) {
            return number_format($bytes / 1_048_576, 2) . ' MiB';
        } else {
            return number_format($bytes / 1_073_741_824, 2) . ' GiB';
        }
    }

    protected static function printResult(array $result, array $headers, string $title): void
    {
        $groupedByAmount = [];

        foreach ($result as $row) {
            $groupedByAmount[$row['amount']][] = $row;
        }

        $result = [];

        foreach ($groupedByAmount as $group) {
            foreach ($group as $row) {
                $result[] = $row;
            }

            $result[] = array_fill(0, count($headers) - 1, '');
        }

        array_pop($result);

        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($result);

        $output->writeln('');
        $output->writeln("<info>$title</info>");
        $table->render();
    }
}
