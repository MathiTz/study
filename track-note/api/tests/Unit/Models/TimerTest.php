<?php

declare(strict_types=1);

namespace TrackNote\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use TrackNote\Models\Timer;

class TimerTest extends TestCase
{
    public function testGetElapsedSecondsForRunningTimer(): void
    {
        $startTime = time() - 3600; // 1 hour ago

        $timer = [
            'started_at' => date('c', $startTime),
            'status' => 'running',
            'total_paused_seconds' => 0,
            'last_pause_at' => null,
        ];

        $elapsed = Timer::getElapsedSeconds($timer);

        $this->assertGreaterThanOrEqual(3600, $elapsed);
        $this->assertLessThanOrEqual(3601, $elapsed);
    }

    public function testGetElapsedSecondsForPausedTimer(): void
    {
        $startTime = time() - 3600; // 1 hour ago
        $pauseTime = time() - 1800; // 30 minutes ago

        $timer = [
            'started_at' => date('c', $startTime),
            'status' => 'paused',
            'total_paused_seconds' => 0,
            'last_pause_at' => date('c', $pauseTime),
        ];

        $elapsed = Timer::getElapsedSeconds($timer);

        // 1 hour total - 30 minutes paused = 30 minutes
        $this->assertGreaterThanOrEqual(1800, $elapsed);
        $this->assertLessThanOrEqual(1801, $elapsed);
    }

    public function testGetElapsedSecondsWithPreviousPauses(): void
    {
        $startTime = time() - 3600; // 1 hour ago

        $timer = [
            'started_at' => date('c', $startTime),
            'status' => 'running',
            'total_paused_seconds' => 600, // 10 minutes already paused
            'last_pause_at' => null,
        ];

        $elapsed = Timer::getElapsedSeconds($timer);

        // 1 hour - 10 minutes = 50 minutes
        $this->assertGreaterThanOrEqual(3000, $elapsed);
        $this->assertLessThanOrEqual(3001, $elapsed);
    }

    public function testGetElapsedSecondsNeverNegative(): void
    {
        $startTime = time() - 100;

        $timer = [
            'started_at' => date('c', $startTime),
            'status' => 'running',
            'total_paused_seconds' => 1000, // More paused than elapsed
            'last_pause_at' => null,
        ];

        $elapsed = Timer::getElapsedSeconds($timer);

        $this->assertEquals(0, $elapsed);
    }

    public function testGetTableReturnsCorrectTableName(): void
    {
        $this->assertEquals('active_timers', Timer::getTable());
    }
}
