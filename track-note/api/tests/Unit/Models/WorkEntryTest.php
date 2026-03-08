<?php

declare(strict_types=1);

namespace TrackNote\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use TrackNote\Models\WorkEntry;

class WorkEntryTest extends TestCase
{
    public function testCalculateDurationForCompletedEntry(): void
    {
        $entry = [
            'started_at' => '2024-01-01 10:00:00',
            'ended_at' => '2024-01-01 12:00:00',
            'total_paused_seconds' => 0,
        ];

        $duration = WorkEntry::calculateDuration($entry);

        $this->assertEquals(7200, $duration); // 2 hours
    }

    public function testCalculateDurationWithPauses(): void
    {
        $entry = [
            'started_at' => '2024-01-01 10:00:00',
            'ended_at' => '2024-01-01 12:00:00',
            'total_paused_seconds' => 1800, // 30 minutes
        ];

        $duration = WorkEntry::calculateDuration($entry);

        $this->assertEquals(5400, $duration); // 1.5 hours
    }

    public function testCalculateDurationNeverNegative(): void
    {
        $entry = [
            'started_at' => '2024-01-01 10:00:00',
            'ended_at' => '2024-01-01 10:30:00',
            'total_paused_seconds' => 7200, // 2 hours (more than duration)
        ];

        $duration = WorkEntry::calculateDuration($entry);

        $this->assertEquals(0, $duration);
    }

    public function testCalculateDurationForOngoingEntry(): void
    {
        $startTime = time() - 3600; // 1 hour ago

        $entry = [
            'started_at' => date('c', $startTime),
            'ended_at' => null,
            'total_paused_seconds' => 0,
        ];

        $duration = WorkEntry::calculateDuration($entry);

        $this->assertGreaterThanOrEqual(3600, $duration);
        $this->assertLessThanOrEqual(3601, $duration);
    }

    public function testGetTableReturnsCorrectTableName(): void
    {
        $this->assertEquals('work_entries', WorkEntry::getTable());
    }
}
